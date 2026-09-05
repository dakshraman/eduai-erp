<?php

namespace App\Providers;

use App\Contracts\FileUploadServiceInterface;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Valuestore\Valuestore;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->ensurePassportKeyPermissions();
        $this->registerOptimizedSharedBindings();
        $this->shareMessengerPosition();

        if ($this->app->runningInConsole()) {
            return;
        }

        /* $host       = request()->getHost();
         $mainDomain = parse_url(config('app.url') ?? '', PHP_URL_HOST);

         $subdomain = $this->extractSubdomain($host, $mainDomain);

         if ($subdomain) {
             URL::defaults(['subdomain' => $subdomain]);
         }*/
    }

    public function register(): void
    {
        $this->app->singleton(FileUploadServiceInterface::class, FileUploadService::class);

        if (! class_exists('App\\ApiBaseMethod', false)) {
            class_alias(\App\Models\ApiBaseMethod::class, 'App\\ApiBaseMethod');
        }

        $this->registerSpondonItBindings();
    }

    private function registerSpondonItBindings(): void
    {
        $interfaces = [
            \SpondonIt\Service\Contracts\AppInitializerInterface::class => \App\Support\SpondonIt\InitServiceAdapter::class,
            \SpondonIt\Service\Contracts\AppInstallerInterface::class => \App\Support\SpondonIt\InstallServiceAdapter::class,
            \SpondonIt\Service\Contracts\AppLifecycleInterface::class => \SpondonIt\Service\Support\NullAppLifecycle::class,
            \SpondonIt\Service\Services\LicenseVerifier::class => \App\Support\SpondonIt\LicenseVerifierAlwaysValid::class,
        ];

        foreach ($interfaces as $interface => $implementation) {
            if (interface_exists($interface) && ! $this->app->bound($interface)) {
                $this->app->bind($interface, $implementation);
            }
        }
    }

    /**
     * Extract the subdomain prefix from the current host.
     *
     * Returns null when the host IS the main domain (no real subdomain prefix),
     * so URL::defaults is never set in that case.
     *
     * ─── BUG THIS FIXES ───────────────────────────────────────────────────────
     *
     * The original code used str_ends_with() then substr() to strip the domain:
     *
     *   str_ends_with('aoraschool.com', 'aoraschool.com') → true   ← matches root!
     *   substr('aoraschool.com', 0, 14 - 15)              → substr(..., 0, -1)
     *   PHP substr with negative 3rd arg strips from end  → 'aoraschool.co'
     *
     * Then: URL::defaults(['subdomain' => 'aoraschool.co'])
     *
     * Laravel's URL generator injects all defaults into route() calls.
     * The 'login' route (defined inside the {subdomain} domain group) picks it up
     * and generates: http://aoraschool.co.aoraschool.com/login
     *
     * ─── FIX ─────────────────────────────────────────────────────────────────
     *
     * Require host to be STRICTLY longer than mainDomain before extracting prefix.
     * If host == mainDomain → length would be 0 or negative → return null.
     */
    private function extractSubdomain(string $host, ?string $mainDomain): ?string
    {

        if (! $mainDomain) {
            // No APP_URL configured (local dev). Handle sub.localhost style.
            $parts = explode('.', $host);
            if (count($parts) === 2 && in_array($parts[1], ['localhost', 'local', 'test'], true)) {
                return $parts[0];
            }

            return count($parts) > 2 ? $parts[0] : null;
        }

        $suffix = '.'.$mainDomain;  // e.g. '.aoraschool.com'

        // Guard: host must be LONGER than the suffix.
        // If host == mainDomain → len(host)=14, len(suffix)=15 → 14 > 15 is FALSE → return null.
        // This prevents the negative substr that was extracting 'aoraschool.co' from 'aoraschool.com'.
        if (mb_strlen($host) > mb_strlen($suffix) && str_ends_with($host, $suffix)) {
            $prefix = mb_substr($host, 0, mb_strlen($host) - mb_strlen($suffix));

            // Reject double subdomains (a.b.example.com) — these are unusual
            // and should not be treated as a single subdomain token.
            if (mb_strlen($prefix) > 0 && ! str_contains($prefix, '.')) {
                return $prefix;
            }
        }

        // Custom domain or root domain — no subdomain to inject
        return null;
    }

    private function shareMessengerPosition(): void
    {
        View::composer('frontEnd.home.front_master', function ($view): void {
            $school = app('school') ?? \App\Models\SmSchool::where('id', 1)->first();
            if (! $school) {
                return;
            }

            $menus = \App\Models\SmHeaderMenuManager::where('theme', 'default')
                ->whereNull('parent_id')
                ->where('school_id', $school->id)
                ->orderBy('position')
                ->get();

            $data = [
                'social_permission' => \App\Models\SmFrontendPersmission::where('name', 'Social Icons')
                    ->where('parent_id', 1)
                    ->where('is_published', 1)
                    ->where('school_id', $school->id)
                    ->first(),
                'menus' => $menus,
                'custom_link' => \App\Models\SmCustomLink::where('school_id', $school->id)->first(),
                'social_icons' => \App\Models\SmSocialMediaIcon::where('school_id', $school->id)
                    ->where('status', 1)
                    ->get(),
                'school' => $school,
                'messenger_position' => 'right',
                'messenger_is_enable' => true,
                'tawk_is_enable' => false,
                'ttl_rtl' => false,
                'is_registration_permission' => false,
                'is_registration_position' => false,
                'reg_setting' => null,
                'school_id' => $school->id,
                'position' => 'right',
            ];

            $view->with($data);
        });

        View::composer('*', function ($view): void {
            $messenger_position = 'right';
            try {
                $chatSettings = storage_path('app/chat/default_settings.json');
                if (file_exists($chatSettings) && filesize($chatSettings) > 0) {
                    $store = Valuestore::make($chatSettings);
                    $messenger_position = $store->get('messenger_position', 'right');
                }
            } catch (\Exception $e) {
                // ignore
            }
            $view->with('messenger_position', $messenger_position);
        });
    }

    private function registerOptimizedSharedBindings(): void
    {
        $this->app->forgetInstance('school_general_settings');
        $this->app->singleton('school_general_settings', function () {
            return generalSetting();
        });

        $this->app->forgetInstance('permission');
        $this->app->singleton('permission', function (): array {
            $user = auth()->user();

            if (! $user) {
                return [];
            }

            $version = function_exists('permissionMenuCacheVersion') ? permissionMenuCacheVersion($user->school_id) : 1;
            $role = Cache::remember('permission_role_'.$user->role_id.'_school_'.$user->school_id.'_v_'.$version, 600, function () use ($user) {
                return \Modules\RolePermission\Entities\InfixRole::select('id', 'is_saas')->find($user->role_id);
            });
            if (! $role) {
                return [];
            }

            $roleType = session()->get('role_permission_user_type', $user->role_id);
            $cacheKey = 'permission_routes_user_'.$user->id.'_role_'.$user->role_id.'_type_'.$roleType.'_school_'.$user->school_id.'_v_'.$version;

            return Cache::remember($cacheKey, 600, function () use ($user, $role): array {
                $permissionIds = \Modules\RolePermission\Entities\AssignPermission::where('role_id', $user->role_id)
                    ->when((int) $role->is_saas === 0, function ($query) use ($user): void {
                        $query->where('school_id', $user->school_id);
                    })
                    ->pluck('permission_id');

                if ($permissionIds->isEmpty()) {
                    return [];
                }

                return \Modules\RolePermission\Entities\Permission::whereIn('id', $permissionIds)
                    ->pluck('route')
                    ->filter()
                    ->values()
                    ->toArray();
            });
        });
    }

    /**
     * Ensure OAuth keys have 600 permissions safely.
     */
    private function ensurePassportKeyPermissions(): void
    {
        if (! function_exists('chmod')) {
            return;
        }

        $keys = ['oauth-private.key', 'oauth-public.key'];

        foreach ($keys as $key) {
            $path = storage_path($key);

            if (file_exists($path)) {
                $currentPerms = fileperms($path) & 0777;

                if ($currentPerms !== 0600) {
                    @chmod($path, 0600);
                }
            }
        }
    }
}
