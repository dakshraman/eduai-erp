<?php

use App\Models\SmAssignSubject;
use App\Models\SmClass;
use App\Models\SmClassSection;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\SmSubject;
use App\Models\StudentRecord;
use App\Models\Theme;
use App\Support\GlobalVariable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Modules\MenuManage\Entities\AlternativeModule;
use Modules\MenuManage\Entities\Sidebar;
use Modules\MenuManage\Entities\SmMenu;
use Modules\RolePermission\Entities\Permission;
use Modules\Branch\Entities\Branch;

if (! function_exists('color_theme')) {
    function color_theme()
    {
        if (! auth()->check()) {
            return userColorThemeActive();
        }
        if (auth()->user()) {
            return userColorThemeActive(auth()->user()->id);
        }

    }

}

if (! function_exists('userColorThemeActive')) {
    function userColorThemeActive(?int $user_id = null)
    {
        $school_id = auth()->user()->school_id ?? 1;
        $cache_key = $user_id ? ('active_theme_user_'.$user_id) : 'active_theme_school_'.$school_id;
        $active_theme = Cache::rememberForever($cache_key, function () use ($user_id) {
            $theme = Theme::with('colors')->where('is_default', 1)
                ->when($user_id, function ($q) use ($user_id) {
                    $q->where('created_by', $user_id);
                })->first();
            if ($user_id && ! $theme) {
                $theme = Theme::with('colors')->where('is_default', 1)->first();
            }
            if (! $theme) {
                $theme = Theme::with('colors')->first();
            }

            return $theme;
        });

        return $active_theme;
    }
}

if (! function_exists('userColorThemes')) {
    function userColorThemes(?int $user_id = null)
    {
        static $reqCache = [];
        $reqKey = $user_id ?? 0;
        if (array_key_exists($reqKey, $reqCache)) {
            return $reqCache[$reqKey];
        }

        $cache_key = $user_id ? ('color_themes_user_'.$user_id) : 'color_themes_all';
        $themes = Cache::remember($cache_key, 1800, function () use ($user_id) {
            $result = Theme::with('colors')
                ->when($user_id, fn ($q) => $q->where('created_by', $user_id))
                ->get();
            if ($user_id && $result->isEmpty()) {
                $result = Theme::with('colors')->where('is_system', 1)->get();
            }

            return $result;
        });

        $reqCache[$reqKey] = $themes;

        return $themes;
    }
}

if (! function_exists('activeStyle')) {
    function activeStyle()
    {
        if (session()->has('active_style') && session()->get('active_style')) {
            $active_style = session()->get('active_style');

            return $active_style;
        }
        $active_style = auth()->check() ? Theme::where('id', auth()->user()->style_id)->first() :
            Theme::where('school_id', 1)->where('is_default', 1)->first();
        if ($active_style === null) {
            $active_style = Theme::where('school_id', 1)->where('is_default', 1)->first();
        }
        if ($active_style === null) {
            $active_style = Theme::first();
        }

        session()->put('active_style', $active_style);

        return session()->get('active_style');

    }
}

if (! function_exists('currency_format_list')) {
    function currency_format_list()
    {
        $symbol = generalSetting()->currency_symbol ?? '$';

        $code = generalSetting()->currency ?? 'USD';
        $formats = [
            ['name' => 'symbol_amount', 'format' => 'symbol(amount) =  '.$symbol.' 1'],
            ['name' => 'amount_symbol', 'format' => 'amount(symbol) = 1'.$symbol],
            ['name' => 'code_amount', 'format' => 'code(amount) = '.$code.' 1'],
            ['name' => 'amount_code', 'format' => 'amount(code) = 1 '.$code],
        ];

        return $formats;
    }
}

if (! function_exists('currency_format')) {
    function currency_format($amount = null, ?string $format = null)
    {
        // if (!$amount) return 0;
        $format = generalSetting()->currencyDetail;
        if (! $format) {
            return number_format((float) $amount, 2, '.', ',');
        }

        $decimal = $format->decimal_digit ?? 0;
        $decimal_separator = $format->decimal_separator ?? '';
        $thousands_separator = $format->thousand_separator ?? '';
        $amount = number_format($amount, $decimal, $decimal_separator, $thousands_separator);
        $symbolCode = $format->currency_type === 'C' ? $format->code : $format->symbol;

        $symbolCodeSpace = $format->space ?
            ($format->currency_position === 'S' ? $symbolCode.' ' : ' '.$symbolCode) : $symbolCode;

        if ($format->currency_position === 'S') {
            return $symbolCodeSpace.$amount;
        }
        if ($format->currency_position === 'P') {
            return $amount.$symbolCodeSpace;
        }
    }
}

if (! function_exists('decimal_only')) {
    function decimal_only($amount = null)
    {
        $format = generalSetting()->currencyDetail;

        if (! $format) {
            return $amount;
        }

        $decimal = $format->decimal_digit ?? 0;

        return number_format($amount, $decimal, '.', '');
    }
}

if (! function_exists('inputStep')) {
    function inputStep(?int $decimalLimit = null): string
    {
        $decimalLimit = $decimalLimit ?? (int) (generalSetting()->currencyDetail->decimal_digit ?? 2);

        return '0.'.str_repeat('0', max($decimalLimit - 1, 0)).'1';
    }
}

if (! function_exists('getDecimalDigit')) {
    function getDecimalDigit(): int
    {
        return (int) (generalSetting()->currencyDetail->decimal_digit ?? 2);
    }
}

if (! function_exists('classes')) {
    function classes(?int $academic_year = null)
    {
        return SmClass::withOutGlobalScopes()
            ->when($academic_year, function ($q) use ($academic_year) {
                $q->where('academic_id', $academic_year);
            }, function ($q) {
                $q->where('academic_id', getAcademicId());
            })->where('school_id', auth()->user()->school_id)
            ->where('active_status', 1)->get();
    }
}

if (! function_exists('sections')) {
    function sections($class_id = null, $academic_year = null)
    {
        if (! $class_id) {
            return null;
        }

        return SmClassSection::withOutGlobalScopes()->where('class_id', $class_id)
            ->where('school_id', auth()->user()->school_id)
            ->when($academic_year, function ($q) use ($academic_year) {
                $q->where('academic_id', $academic_year);
            }, function ($q) {
                $q->where('academic_id', getAcademicId());
            })->get();

    }
}

if (! function_exists('subjects')) {
    function subjects(int $class_id, int $section_id, ?int $academic_year = null)
    {
        $subjects = SmAssignSubject::withOutGlobalScopes()
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('school_id', auth()->user()->school_id)
            ->when($academic_year, function ($q) use ($academic_year) {
                $q->where('academic_id', $academic_year);
            }, function ($q) {
                $q->where('academic_id', getAcademicId());
            })->select('class_id', 'section_id', 'subject_id')->distinct(['class_id', 'section_id', 'subject_id'])->get();

        return $subjects;

    }
}

if (! function_exists('students')) {
    function students(int $class_id, ?int $section_id = null, ?int $academic_year = null)
    {
        $student_ids = StudentRecord::where('class_id', $class_id)
            ->when($section_id, function ($q) use ($section_id) {
                $q->where('section_id', $section_id);
            })->when($academic_year, function ($q) use ($academic_year) {
                $q->where('academic_id', $academic_year);
            }, function ($q) {
                $q->where('academic_id', getAcademicId());
            })->where('school_id', auth()->user()->school_id)->pluck('student_id')->unique()->toArray();

        $students = SmStudent::withOutGlobalScopes()->whereIn('id', $student_ids)->get();

        return $students;

    }
}

if (! function_exists('classSubjects')) {
    function classSubjects($class_id = null)
    {
        $subjects = SmAssignSubject::query();
        if (teacherAccess()) {
            $subjects->where('teacher_id', auth()->user()->staff->id);
        }
        if ($class_id !== 'all_class') {
            $subjects->where('class_id', '=', $class_id);
        } else {
            $subjects->distinct('class_id');
        }
        $subjectIds = $subjects->distinct('subject_id')->get()->pluck(['subject_id'])->toArray();

        return SmSubject::whereIn('id', $subjectIds)->get(['id', 'subject_name']);
    }
}
if (! function_exists('subjectSections')) {
    function subjectSections($class_id = null, $subject_id = null)
    {

        if (! $class_id || ! $subject_id) {
            return null;
        }

        $sectionIds = SmAssignSubject::where('class_id', $class_id)
            ->where('subject_id', '=', $subject_id)
            ->where('school_id', auth()->user()->school_id)
            ->when(teacherAccess(), function ($q) {
                $q->where('teacher_id', auth()->user()->staff->id);
            })
            ->distinct(['class_id', 'section_id'])
            ->pluck('section_id')
            ->toArray();

        return SmSection::whereIn('id', $sectionIds)->get(['id', 'section_name']);

    }
}

if (! function_exists('routeIsExist')) {
    function routeIsExist($route, $children_id = null)
    {
        if ($children_id) {
            if (Route::has($route)) {
                return true;
            }
        }
        if (Route::has($route)) {
            return true;
        }

        return false;

    }
}

if (! function_exists('validRouteUrl')) {
    function validRouteUrl($route, $children_id = null)
    {
        $url = null;
        try {
            if (routeIsExist($route, $children_id)) {
                if ($children_id) {
                    $url = \route($route, $children_id);
                } else {
                    $url = \route($route);
                }

            } else {
                $url = route('user-custom-menu.index', $route);
            }
        } catch (Exception $e) {
        }

        return $url;
    }
}

if (! function_exists('routeIs')) {
    function routeIs($route)
    {
        if (Route::currentRouteName() === $route) {
            return true;
        }

        return false;

    }
}
if (! function_exists('subModuleRoute')) {
    function subModuleRoute($menu, $routes = [], $useCache = true)
    {
        // Cache key unique per school and top-level menu
        $cacheKey = 'submenu_routes_'.auth()->user()->school_id.'_'.$menu->id;

        if ($useCache) {
            return Cache::remember($cacheKey, 60, function () use ($menu, $routes) {
                return subModuleRoute($menu, $routes, false); // recursion without caching
            });
        }

        // Collect route from current menu
        if (! empty($menu->route)) {
            $routes[] = $menu->route;
        }

        // Recurse through child menus
        if ($menu->childs && $menu->childs->count() > 0) {
            foreach ($menu->childs as $child) {
                $routes = subModuleRoute($child, $routes, false);
            }
        }

        return $routes;
    }
}

// if(!function_exists('getSubModuleRoutes'))
// {
//     function getSubModuleRoutes($menu, $routes = [])
//     {

//         $routes[] = $menu->route;
//         if($menu->childs->count() ){
//             foreach($menu->childs as $child)
//             {

//                 //$routes = getSubModuleRoutes($child, $routes);
//             }
//             return $routes;
//         }
//         return $routes;
//     }
// }

if (! function_exists('deActivePermissions')) {
    function deActivePermissions()
    {
        $alternativeDeActiveModuleInfo = AlternativeModule::where('status', 0)->pluck('module_name')->toArray();

        return Permission::whereIn('module', $alternativeDeActiveModuleInfo)->pluck('id')->toArray();

    }
}

if (! function_exists('permissionMenuCacheVersionKey')) {
    function permissionMenuCacheVersionKey(?int $school_id = null): string
    {
        $school_id = $school_id ?: (auth()->user()->school_id ?? (app()->bound('school') ? app('school')->id : 1));

        return 'permission_menu_cache_version_'.$school_id;
    }
}

if (! function_exists('permissionMenuCacheVersion')) {
    function permissionMenuCacheVersion(?int $school_id = null): int
    {
        static $versions = [];

        $key = permissionMenuCacheVersionKey($school_id);
        if (array_key_exists($key, $versions)) {
            return $versions[$key];
        }

        return $versions[$key] = (int) Cache::get($key, 1);
    }
}

if (! function_exists('clearPermissionMenuCache')) {
    function clearPermissionMenuCache(?int $school_id = null): void
    {
        $key = permissionMenuCacheVersionKey($school_id);

        Cache::forever($key, ((int) Cache::get($key, 1)) + 1);
    }
}

function sidebar_cache_key($role_id = null)
{

    $user = auth()->user();

    return 'sidebar_user_'.$user->id.'_role_'.$role_id.'_school_'.$user->school_id.'_v_'.permissionMenuCacheVersion($user->school_id);
}

function is_role_based_sidebar()
{
    if (app()->bound('school_general_settings') && app('school_general_settings')) {
        return app('school_general_settings')->role_based_sidebar;
    }

    return false;
}

if (! function_exists('sidebar_menus')) {
    function sidebar_menus($role_id = null)
    {
        static $requestCache = [];

        $user = auth()->user();
        if (! $role_id) {
            $role_id = $user->role_id;
        }
        $is_role_based_sidebar = is_role_based_sidebar();
        $cacheKey = sidebar_cache_key($role_id);

        if (array_key_exists($cacheKey, $requestCache)) {
            return $requestCache[$cacheKey];
        }

        // Cache::forget(sidebar_cache_key($role_id));
        return $requestCache[$cacheKey] = Cache::remember($cacheKey, 300, function () use ($role_id, $is_role_based_sidebar, $user) {
            return Sidebar::with(['subModule' => function ($q) use ($role_id, $is_role_based_sidebar) {
                $q->when($is_role_based_sidebar, function ($q) use ($role_id) {
                    $q->where('role_id', $role_id)
                        ->whereHas('permissionInfo', function ($q) use ($role_id) {
                            $q->where('menu_status', 1)->when($role_id === 2, function ($q) {
                                $q->where('is_student', 1);
                            })->when($role_id === 3, function ($q) {
                                $q->where('is_parent', 1);
                            })->when(! in_array($role_id, [2, 3]), function ($q) {
                                $q->where('is_admin', 1)->orWhere('is_teacher', 1);
                            });
                        })
                        ->with(['subModule' => function ($q) use ($role_id) {
                            $q->where('role_id', $role_id)->whereHas('permissionInfo', function ($q) use ($role_id) {
                                $q->where('menu_status', 1)->when($role_id === 2, function ($q) {
                                    $q->where('is_student', 1);
                                })->when($role_id === 3, function ($q) {
                                    $q->where('is_parent', 1);
                                })->when($role_id === 4, function ($q) {
                                    $q->where(function ($q) {
                                        $q->where('is_admin', 1)->orWhere('is_teacher', 1);
                                    });
                                })->when(! in_array($role_id, [2, 3]), function ($q) {
                                    $q->where('is_admin', 1)->orWhere('is_teacher', 1);
                                });
                            });
                        }]);
                });
            }, 'permissionInfo' => function ($q) {
                $q->when(moduleStatusCheck('CustomMenu'), function ($q) {
                    $q->with('customMenu');
                });
            }])
                ->whereNull('parent')
                ->whereHas('permissionInfo', function ($q) use ($role_id) {
                    $q->where('menu_status', 1)
                        ->when($role_id === 2 || $role_id === GlobalVariable::isAlumni(), function ($q) {
                            $q->where('is_student', 1);
                        })->when($role_id === 3, function ($q) {
                            $q->where('is_parent', 1);
                        })->when($role_id === 4, function ($q) {
                            $q->where(function ($q) {
                                $q->where('is_admin', 1)->orWhere('is_teacher', 1);
                            });
                        })->when(! in_array($role_id, [2, 3, 4, GlobalVariable::isAlumni()]), function ($q) {
                            $q->where('is_admin', 1);
                        });
                })
                ->when(! $is_role_based_sidebar, function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }, function ($q) {
                    $q->whereNull('user_id');
                })
                ->where('role_id', $role_id)->where('active_status', 1)
                ->orderBy('position', 'ASC')->get();
        });
    }
}

if (! function_exists('getMenus')) {
    function getMenus($role_name = 'staff')
    {
        static $requestCache = [];

        $user = auth()->user();
        $catch_for = 'sm_menus_v3_user_'.$user->id.'_role_'.$user->role_id.'_type_'.$role_name.'_school_'.$user->school_id.'_v_'.permissionMenuCacheVersion($user->school_id);

        if (array_key_exists($catch_for, $requestCache)) {
            return $requestCache[$catch_for];
        }

        return $requestCache[$catch_for] = Cache::remember($catch_for, 600, function () use ($role_name) {
            return SmMenu::with([
                'childs' => function ($q) use ($role_name) {
                    $q->where('menu_status', 1)
                        ->when($role_name === 'student', function ($q) {
                            $q->where('role_id', 2);
                        })
                        ->when($role_name === 'parent', function ($q) {
                            $q->where('role_id', 3);
                        })
                        ->where('school_id', auth()->user()->school_id)
                        ->orderBy('position', 'ASC');
                },
                'childs.childs' => function ($q) use ($role_name) {
                    $q->where('menu_status', 1)->when($role_name === 'student', function ($q) {
                        $q->where('role_id', 2);
                    })
                        ->when($role_name === 'parent', function ($q) {
                            $q->where('role_id', 3);
                        })
                        ->when($role_name === 'staff', function ($q) {
                            $q->where('role_id', 1);
                        })
                        ->where('school_id', auth()->user()->school_id)
                        ->orderBy('position', 'ASC');
                },
                'childs.childs.childs' => function ($query) use ($role_name) {
                    $query->where('menu_status', 1)
                        ->when($role_name === 'student', function ($q) {
                            $q->where('role_id', 2);
                        })
                        ->when($role_name === 'parent', function ($q) {
                            $q->where('role_id', 3);
                        })
                        ->when($role_name === 'staff', function ($q) {
                            $q->where('role_id', 1);
                        })
                        ->where('school_id', auth()->user()->school_id)
                        ->orderBy('position', 'ASC');
                },
                'childs.childs.childs.childs' => function ($query) use ($role_name) {
                    $query->where('menu_status', 1)
                        ->when($role_name === 'student', function ($q) {
                            $q->where('role_id', 2);
                        })
                        ->when($role_name === 'parent', function ($q) {
                            $q->where('role_id', 3);
                        })
                        ->when($role_name === 'staff', function ($q) {
                            $q->where('role_id', 1);
                        })
                        ->where('school_id', auth()->user()->school_id)
                        ->orderBy('position', 'ASC');
                },
                'childs.childs.childs.childs.childs' => function ($query) use ($role_name) {
                    $query->where('menu_status', 1)
                        ->when($role_name === 'student', function ($q) {
                            $q->where('role_id', 2);
                        })
                        ->when($role_name === 'parent', function ($q) {
                            $q->where('role_id', 3);
                        })
                        ->when($role_name === 'staff', function ($q) {
                            $q->where('role_id', 1);
                        })
                        ->where('school_id', auth()->user()->school_id)
                        ->orderBy('position', 'ASC');
                }])
                ->where('menu_status', 1)
                ->where('school_id', auth()->user()->school_id)
                ->where('permission_section', 1)
                ->when($role_name === 'student', function ($q) {
                    $q->where('role_id', 2);
                })
                ->when($role_name === 'parent', function ($q) {
                    $q->where('role_id', 3);
                })
                ->when($role_name === 'staff', function ($q) {
                    $q->where('role_id', 1);
                })
                ->orderBy('position', 'ASC')->get();
        });
    }
}

if (! function_exists('getUnusedMenus')) {
    function getUnusedMenus($role_name = 'staff')
    {

        $sectionIds = SmMenu::whereNull('parent')
            ->when($role_name === 'student', function ($q) {
                $q->where('role_id', 2)->where('school_id', auth()->user()->school_id);
            })
            ->when($role_name === 'parent', function ($q) {
                $q->where('role_id', 3)->where('school_id', auth()->user()->school_id);
            })
            ->when($role_name === 'staff', function ($q) {
                $q->where('role_id', 1)->where('school_id', auth()->user()->school_id);
            })
            ->where('menu_status', 0)
            ->where('school_id', auth()->user()->school_id)
            ->pluck('id')->toArray();

        $parentSidebars = SmMenu::whereIn('parent', $sectionIds)
            ->where('menu_status', 0)
            ->when($role_name === 'student', function ($q) {
                $q->where('role_id', 2)->where('school_id', auth()->user()->school_id);
            })
            ->when($role_name === 'parent', function ($q) {
                $q->where('role_id', 3)->where('school_id', auth()->user()->school_id);
            })
            ->when($role_name === 'staff', function ($q) {
                $q->where('role_id', 1)->where('school_id', auth()->user()->school_id);
            })
            ->where('school_id', auth()->user()->school_id)
            ->pluck('id')
            ->toArray();

        $single = SmMenu::whereNotIn('parent', $parentSidebars)
            ->where('menu_status', 0)
            ->when($role_name === 'student', function ($q) {
                $q->where('role_id', 2)->where('school_id', auth()->user()->school_id);
            })
            ->when($role_name === 'parent', function ($q) {
                $q->where('role_id', 3)->where('school_id', auth()->user()->school_id);
            })
            ->when($role_name === 'staff', function ($q) {
                $q->where('role_id', 1)->where('school_id', auth()->user()->school_id);
            })
            ->where('school_id', auth()->user()->school_id)
            ->pluck('id')
            ->toArray();
        $hasIds = array_merge($parentSidebars, $single);

        $hasIds = (array_unique($hasIds));

        if ($hasIds !== []) {
            return SmMenu::whereIn('id', $hasIds)->get();
        }

        return collect();
    }
}

if (! function_exists('isTeacher')) {
    function isTeacher()
    {
        if (auth()->user()->role_id === 4) {
            return true;
        }

        return false;
    }
}

if (! function_exists('storePermissionData')) {
    function storePermissionData($permission, $user_id = null, $school_id = null, $role_id = null)
    {
        // Skip permission data seeding in test environment to speed up migrations
        if (app()->environment('testing')) {
            return;
        }

        $is_role_based_sidebar = is_role_based_sidebar();
        Permission::updateOrCreate([
            'module' => $permission['module'],
            'sidebar_menu' => $permission['sidebar_menu'],
            'lang_name' => $permission['lang_name'],
            'icon' => $permission['icon'],
            'svg' => $permission['svg'],
            'route' => $permission['route'],
            'parent_route' => $permission['parent_route'],
            'is_admin' => $permission['is_admin'],
            'is_teacher' => $permission['is_teacher'],
            'is_student' => $permission['is_student'],
            'is_parent' => $permission['is_parent'],
            'is_saas' => $permission['is_saas'],
            'is_menu' => $permission['is_menu'],
            'status' => $permission['status'],
            'menu_status' => $permission['menu_status'],
            'relate_to_child' => $permission['relate_to_child'],
            'alternate_module' => $permission['alternate_module'],
            'permission_section' => $permission['permission_section'],
            'type' => $permission['type'],
            'role_id' => $role_id,
            'old_id' => $permission['old_id'],
            'school_id' => $school_id ?? 1,
        ],
            [
                'name' => $permission['name'],
                'position' => $permission['position'],

            ]);

        if (isset($permission['child'])) {
            foreach ($permission['child'] as $child) {
                storePermissionData($child);
            }
        }
    }
}

if (! function_exists('sidebarPermission')) {
    function sidebarPermission($permission)
    {
        return true;
        /*if (!$permission) return false;
        $user = auth()->user();
        if ($permission->permission_section == 1) return true;

        if($permission->route == 'menumanage.index' && is_role_based_sidebar()){
            if(moduleStatusCheck('Saas')){
                return false;
            }
            if(auth()->user()->role_id == 1){
                return true;
            }
            return false;
        }

        if(moduleStatusCheck('CustomMenu') && $permission->customMenu) {
            $menu = $permission->customMenu;
            $user      = Auth::user();
            $role_id    = $user->role_id;
            $school_id  = $user->school_id;

            $available_for  = json_decode($menu->available_for, true);
            $school_ids     = json_decode($menu->school_id, true);

            return in_array($role_id, $available_for) && in_array($school_id, $school_ids);
        }

        if ($permission->module && $permission->module != 'fees_collection') {
            if (moduleStatusCheck($permission->module)) {
                $access = true;
                if ($permission->module == 'Saas') {
                    $saasNotAdministrator = ['administrator-notice', 'school/ticket-view', 'subscription/history'];
                    $subscriptions = ['subscription/package-list', 'subscription/history'];
                    if ($permission->route == 'saas.custom-domain') {
                        $access = config('app.allow_custom_domain') ? true : false;
                    }
                    if ($permission->route == 'school-general-settings') {
                        $access = isSubscriptionEnabled() && $user->is_administrator == 'yes' ? true : false;
                    }
                    if (in_array($permission->route, $saasNotAdministrator)) {
                        $access = $user->is_administrator != 'yes' ? true : false;
                    }
                    if (in_array($permission->route, $subscriptions)) {
                        $access = isSubscriptionEnabled() && $user->is_administrator != 'yes' ? true : false;
                    }
                }
            } else {
                $access = false;
            }

        } elseif (!$permission->module) {
            $access = true;
        }

      */

        if (moduleStatusCheck('Saas') && $route) {
            if (! $permission->module || $permission->alternate_module === 'OnlineExam') {
                $access = isMenuAllowToShow($permission->sidebar_menu);
            }
        }

        if (userPermission($permission->$route) === true && $access === true) {
            return true;
        }

        return false;
    }
}

if (! function_exists('ignorePermissionRoutes')) {
    function ignorePermissionRoutes()
    {
        return ['reports', 'system_settings', 'front_settings', 'fees.fees-report', 'exam-setting'];
    }
}
if (! function_exists('ignorePermissionIds')) {
    function ignorePermissionIds()
    {
        return Permission::whereIn('route', ignorePermissionRoutes())->pluck('id')->toArray();
    }
}

if (! function_exists('assetPath')) {
    function assetPath($url = null)
    {
        if (! $url) {
            return asset('');
        }

        // Normalize backslashes to forward slashes
        $url = str_replace('\\', '/', $url);

        // Strip full domain URLs if present
        $baseUrl = asset('/');
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $parsedUrl = parse_url($url);
            $url = $parsedUrl['path'] ?? $url;
        }

        // Clean leading slashes and duplicate 'public/'
        $url = ltrim($url, '/');
        if (str_starts_with($url, 'public/')) {
            $url = substr($url, 7);
        }

        if (config('app.has_public_folder', true)) {
            $fullUrl = asset('public/'.$url);
        } else {
            $fullUrl = asset($url);
        }

        // Match current subdomain origin
        if (request()->getHttpHost() !== parse_url(config('app.url'), PHP_URL_HOST)) {
            $parsed = parse_url($fullUrl);
            $scheme = request()->getScheme();
            $host = request()->getHttpHost();
            $path = $parsed['path'] ?? '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            return $scheme . '://' . $host . $path . $query;
        }

        return $fullUrl;

    }
}

function pwa_assetPath($path = null){
    if (! $path) {
        return '';
    }
    $path = ltrim($path, '/');
    $path = str_replace('public/', '', $path);
    if (config('app.has_public_folder')) {
        $path = str_replace(config('app.url').'/', '', $path);
        return config('app.url').'/public/'.$path;
    }

    return config('app.url').'/'.$path;

}

if (! function_exists('getAllParentBranches')) {
    function getAllParentBranches()
    {
        return Branch::withOutGlobalScopes()->where('status', 1)->get();
    }
}

// Get shifts by branch
if (! function_exists('getBranchShifts')) {
    function getBranchShifts($branch_id, $academic_id = null)
    {
        try {
            $query = \App\Models\Shift::where('active_status', 1)
                ->where('school_id', auth()->user()->school_id ?? 1)
                ->withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class);

            if ($branch_id) {
                $query->where(function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id)
                        ->orWhereNull('branch_id')
                        ->orWhere('branch_id', 0);
                });
            }
            
            if ($academic_id) {
                $query->where('academic_id', $academic_id);
            }
            
            $shifts = $query->orderBy('name')->get(['id', 'name']);
            return $shifts;
        } catch (\Exception $e) {
            return [];
        }
    }
}

// Get classes by branch
if (! function_exists('getBranchClasses')) {
    function getBranchClasses($branch_id, $academic_id = null)
    {
        try {
            $query = \App\Models\SmClass::where('active_status', 1)
                ->where('school_id', auth()->user()->school_id ?? 1)
                ->withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class);

            if ($branch_id) {
                $query->where(function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id)
                        ->orWhereNull('branch_id')
                        ->orWhere('branch_id', 0);
                });
            }
            
            if ($academic_id) {
                $query->where('academic_id', $academic_id);
            }
            
            $classes = $query->orderBy('class_name')->get(['id', 'class_name', 'shift_id']);
            return $classes;
        } catch (\Exception $e) {
            return [];
        }
    }
}

// Get classes by shift from SmClassSection
if (!function_exists('getShiftClasses')) {
    function getShiftClasses($shift_id, $branch_id = null, $academic_id = null)
    {
        try {
            $sectionQuery = SmClassSection::where('school_id', auth()->user()->school_id ?? 1)
                ->withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)
                ->where('shift_id', $shift_id);

            // Apply filters BEFORE pluck
            if ($branch_id) {
                $sectionQuery->where('branch_id', $branch_id);
            }

            if ($academic_id) {
                $sectionQuery->where('academic_id', $academic_id);
            }

            // Now pluck filtered class_ids
            $classQueryIds = $sectionQuery->pluck('class_id')->unique();

            $classes = \App\Models\SmClass::whereIn('id', $classQueryIds)
                ->where('active_status', 1)
                ->where('school_id', auth()->user()->school_id ?? 1)
                ->withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)
                ->orderBy('class_name')
                ->get(['id', 'class_name', 'branch_id', 'shift_id', 'academic_id']);

            return branchWise($classes);

        } catch (\Exception $e) {
            return [];
        }
    }
}
