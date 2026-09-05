<?php

use App\Models\SmSchool;
use Carbon\Carbon;
use App\Models\InfixModuleManager;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Modules\Saas\Entities\SmSubscriptionPayment;

if (! function_exists('getVar')) {
    function getVar(string $list)
    {
        $file = resource_path('var/'.$list.'.json');

        return (File::exists($file)) ? json_decode(file_get_contents($file), true) : [];
    }
}

if (! function_exists('getModuleVar')) {
    function getModuleVar($module, string $list)
    {
        if (! Module::find($module)) {
            return [];
        }

        $file = module_path($module, 'Resources/var/'.$list.'.json');

        return (File::exists($file)) ? json_decode(file_get_contents($file), true) : [];
    }
}

function getPlanPermissionMenuModuleId()
{
    return collect(planPermissions('menus', true))->values()->filter(function ($v): bool {
        return ! is_null($v);
    })->toArray();
}

/**
 * planPermissions — returns the full plan permission data structure.
 *
 * OPTIMIZED: Result is memoized in a static variable so the JSON file reads
 * and cache lookups only happen ONCE per request, not once per module.
 *
 * Previously called 71x (once per module inside ModuleRegistry::computeStatus),
 * each time reading JSON files and doing multiple cache lookups.
 */
function planPermissions($hook = null, $module_id = false)
{
    // Static memoization — computed once per request, keyed by $module_id flag
    static $memo = [];
    $memo_key = $module_id ? 'with_id' : 'without_id';

    if (! isset($memo[$memo_key])) {
        $modules = Cache::rememberForever('paid_modules', function () {
            return InfixModuleManager::where('name', '!=', 'Saas')->where('is_default', '!=', 1)->pluck('name')->toArray();
        });
        $default_modules = Cache::rememberForever('default_modules', function () {
            return InfixModuleManager::where('name', '!=', 'Saas')->where('is_default', 1)->pluck('name')->toArray();
        });

        $menus = collect(getVar('limits'));

        foreach ($default_modules as $default_module) {
            $menus = $menus->merge(getModuleVar($default_module, 'limits'));
        }

        $menus = $menus->map(function ($v) use ($module_id) {
            return gv($v, $module_id ? 'module_id' : 'lang');
        })->toArray();

        $final_modules = [];

        foreach ($modules as $module) {
            $m = Module::find($module);
            if ($m && $m->isEnabled($module)) {
                $is_verify = Cache::rememberForever('module_'.$module, function () use ($module) {
                    return InfixModuleManager::where('name', $module)->first();
                });
                if ($is_verify && $is_verify->purchase_code) {
                    $final_modules[$module] = $module;
                }
            }
        }

        $memo[$memo_key] = ['modules' => $final_modules, 'menus' => $menus];
    }

    $data = $memo[$memo_key];

    if (! $hook) {
        return $data;
    }

    return gv($data, $hook);
}

function saasSettings($key): bool
{
    if (! moduleStatusCheck('Saas')) {
        return false;
    }

    $settings = Cache::rememberForever('saas_settings', function () {
        return Modules\Saas\Entities\SaasSettings::all();
    });

    $saas = $settings->where('route', $key)->first();
    if ($saas) {
        return $saas->saas_status == 1 ? true : false;
    }

    return false;
}

function isSubscriptionEnabled(): bool
{
    return saasSettings('manage_subscription');
}

function isMenuAllowToShow($menu)
{
    if (! moduleStatusCheck('Saas')) {
        return true;
    }

    if (auth()->user()->role_id == 2 || auth()->user()->role_id == 3) {
        $studentRoutes = getStudentRoutes();
        if (empty($studentRoutes[$menu])) {
            return true;
        }
        $menu = $studentRoutes[$menu];
    }
    if (isSubscriptionEnabled() == false) {
        return true;
    }

    $school = getSchool();

    if ($school->id == 1) {
        return true;
    }

    $school_module = getSchoolModule($school);
    $school_module_status = false;
    if ($school_module && (in_array($menu, $school_module->menus) || in_array($menu, $school_module->modules))) {

        return true;
    }

    $active = activePackage();
    $package_module_status = false;
    if (in_array($menu, $active->menus) || in_array($menu, $active->modules)) {
        return true;
    }

    return $package_module_status == true && $school_module_status == true ? true : false;
}

/**
 * getSchool — resolve the current school.
 *
 * OPTIMIZED: Memoized in a static variable so the auth()->user()->school
 * Eloquent relation load only fires once per request, not once per module check.
 *
 * Previously called up to 71x per request (once per module in ModuleRegistry)
 * causing 71 Eloquent relation queries when the school wasn't bound in the container.
 */
function getSchool()
{
    static $school = null;

    if ($school !== null) {
        return $school;
    }

    if (app()->bound('school')) {
        $school = app('school');
    } elseif (auth()->check()) {
        $school = auth()->user()->school;
    } else {
        $school = SmSchool::first();
    }

    return $school;
}

function isModuleForSchool($module): bool
{
    $school = getSchool();

    if ($school->id == 1) {
        return true;
    }
    if (! isSubscriptionEnabled()) {
        return true;
    }

    $school_module = getSchoolModule($school);

    $active_modules = $school_module?->modules ?? [];
    $active_menus = $school_module?->menus ?? [];
    $all_active = array_merge($active_modules, $active_menus);

    if (in_array($module, $all_active)) {
        return true;
    }

    $package_modules = activePackage();
    if (! $package_modules) {
        return false;
    }

    $package_active_modules = is_array($package_modules?->modules) ? $package_modules->modules : [];
    $package_active_menus = is_array($package_modules?->menus) ? $package_modules->menus : [];
    $all_package_menus = array_merge($package_active_modules, $package_active_menus);

    if (in_array($module, $all_package_menus)) {
        return true;
    }

    return false;
}

function strToLowerArray($array): array
{
    return array_map(function ($m) {
        return mb_strtolower($m);
    }, $array);
}

/**
 * getSchoolModule — fetch the SchoolModule record for a school.
 *
 * OPTIMIZED: Memoized in a static variable keyed by school ID.
 * Previously called inside isModuleForSchool() which itself was called
 * once per module in ModuleRegistry — 71 cache lookups per request.
 * Now: first call per school hits the cache, all others return instantly.
 */
function getSchoolModule($school = null)
{
    static $memo = [];

    if (! $school) {
        $school = getSchool();
    }

    $key = $school->id;
    if (isset($memo[$key])) {
        return $memo[$key];
    }

    $memo[$key] = Cache::rememberForever('school_modules'.$school->id, function () use ($school) {
        return App\Models\SchoolModule::where('school_id', $school->id)->first();
    });

    return $memo[$key];
}

/**
 * activePackage — resolve the currently active subscription package.
 *
 * OPTIMIZED: Memoized in a static variable keyed by school ID.
 * Previously called once per module check (71x), each time doing a
 * cache lookup + potential multi-query subscription resolution.
 * Now: one cache hit per school per request.
 */
function activePackage($school = null)
{
    static $memo = [];

    if (! $school) {
        $school = getSchool();
    }

    $key = $school->id;
    if (isset($memo[$key])) {
        return $memo[$key];
    }

    $memo[$key] = Cache::remember('active_package'.$school->id, Carbon::now()->endOfDay()->addSecond(), function () use ($school) {
        $last_record = SmSubscriptionPayment::with('package')->orderBy('id', 'desc')->where(function ($q): void {
            $q->where('approve_status', 'approved')->orWhere('payment_type', 'trial');
        })->where('school_id', $school->id)->first();

        if (! $last_record) {
            return false;
        }

        $now_time = date('Y-m-d');
        $purchase_packages = SmSubscriptionPayment::with('package')->where('school_id', $school->id)->get();

        $last_active = SmSubscriptionPayment::with('package')->orderBy('id', 'desc')
            ->where('approve_status', 'approved')
            ->where('start_date', '<=', $now_time)
            ->where('end_date', '>=', $now_time)
            ->where('school_id', $school->id)->first();

        if (! $purchase_packages->count()) {
            return false;
        }

        $package = null;

        foreach ($purchase_packages as $purchase_package) {
            if ($last_record && $last_record->buy_type == 'instantly' && $last_record->approve_status == 'approved') {
                if ($now_time >= $purchase_package->start_date && $now_time <= $purchase_package->end_date && $purchase_package->id == $last_record->id && $purchase_package->approve_status == 'approved') {
                    $package = $purchase_package;
                }
            } elseif ($last_record && $last_record->buy_type == 'buy_now' && $last_record->approve_status == 'approved') {
                if ($now_time >= $purchase_package->start_date && $now_time <= $purchase_package->end_date && $purchase_package->approve_status == 'approved' && $last_active->id == $purchase_package->id) {
                    $package = $purchase_package;
                }
            } elseif ($last_record && $last_record->payment_type == 'trial') {
                if ($now_time >= $purchase_package->start_date && $now_time <= $purchase_package->end_date && $purchase_package->payment_type == 'trial') {
                    $package = $purchase_package;
                }
            }
        }

        if ($package) {
            return $package->package;
        }

        return false;
    });

    return $memo[$key];
}

function getModuleAdminSection($module_id)
{
    $school_permissions = planPermissions('menus', true);
    $key = false;
    foreach ($school_permissions as $permission => $id) {
        if ($id == $module_id) {
            $key = $permission;
            break;
        }
    }

    return $key;
}

if (! function_exists('changeRouteForStudentParent')) {
    function changeRouteForStudentParent($menu)
    {
        //
    }
}

if (! function_exists('getStudentRoutes')) {
    function getStudentRoutes()
    {
        return [
            'student_my_attendance'    => null,
            'student_class_routine'    => null,
            'examination'              => null,
            'student_noticeboard'      => null,
            'student_subject'          => null,
            'online_exam'              => null,
            'student_teacher'          => null,
            'student_transport'        => 'student_transport',
            'student_dormitory'        => 'dormitory',
            'student-profile'          => 'null',
            'student-dashboard'        => null,
            'fees.student-fees-list'   => 'fees',
            'lesson-plan'              => 'lesson-plan',
            'student_homework'         => 'student_homework',
            'download_center'          => 'study_material',
            'leave'                    => 'leave',
            'chat'                     => 'chat',
        ];
    }
}

if (!function_exists('generateSchoolSubdomain')) {
    /**
     * Generate a unique subdomain suggestion from a school name.
     * E.g. "Green Valley School" => "gvs" (or "gvs1" if taken)
     * Only a-z, 0-9 allowed, lowercase.
     */
    function generateSchoolSubdomain($schoolName)
    {
        $base = strtolower(preg_replace('/[^aA-zZ0-9 ]/', '', $schoolName));
        $words = preg_split('/\s+/', trim($base));
        if (count($words) > 1) {
            $subdomain = '';
            foreach ($words as $w) {
                if ($w !== '') {
                    $subdomain .= $w[0];
                }
            }
        } else {
            $subdomain = $base;
        }
        $subdomain = preg_replace('/[^a-z0-9]/', '', $subdomain);
        if ($subdomain === '') {
            $subdomain = 'school';
        }
        $original = $subdomain;
        $i = 1;
        while (\DB::table('sm_schools')->where('domain', $subdomain)->exists()) {
            $subdomain = $original . $i;
            $i++;
        }
        return $subdomain;
    }
}
