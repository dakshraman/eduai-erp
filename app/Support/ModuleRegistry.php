<?php

namespace App\Support;

use App\Models\InfixModuleManager;
use Illuminate\Support\Facades\Cache;
use Nwidart\Modules\Facades\Module;
use Throwable;

/**
 * ModuleRegistry — Single source of truth for module status checks.
 *
 * PROBLEM SOLVED:
 *   The old moduleStatusCheck() helper was called 2,236+ times per request.
 *   Each call hit the session, the DB, and the filesystem.
 *   This class computes every module status ONCE at first access,
 *   stores in a static array for the lifetime of the request,
 *   and serves all subsequent checks from memory (zero DB, zero filesystem).
 *
 * USAGE:
 *   // Old (slow — DB + filesystem on every call):
 *   if (moduleStatusCheck('University')) { ... }
 *
 *   // New (fast — memory lookup after first call):
 *   if (ModuleRegistry::isActive('University')) { ... }
 *
 *   // The global helper still works — it now delegates here:
 *   if (moduleStatusCheck('University')) { ... }  // backward compatible
 */
class ModuleRegistry
{
    /**
     * List of all module names stored in DB (cached across requests).
     * TTL: 10 minutes — low-risk, modules rarely change at runtime.
     */
    private const DB_CACHE_KEY = 'module_registry_modules';

    private const OLD_DB_CACHE_KEY = 'module_registry_all_modules';

    private const DB_CACHE_TTL = 600; // 10 minutes

    /**
     * Per-request in-memory cache of module statuses.
     * Populated once on first access, never re-queried within the same request.
     *
     * @var array<string, bool>|null
     */
    private static ?array $statuses = null;

    /**
     * Check if a module is active.
     * Computes all statuses on first call, then serves from static array.
     */
    public static function isActive(string $module): bool
    {
        if (self::$statuses === null) {
            self::boot();
        }

        return self::$statuses[$module] ?? false;
    }

    /**
     * Get all active module names.
     */
    public static function activeModules(): array
    {
        if (self::$statuses === null) {
            self::boot();
        }

        return array_keys(array_filter(self::$statuses));
    }

    /**
     * Invalidate the per-request cache and the file cache.
     * Call this after installing/uninstalling a module.
     */
    public static function invalidate(): void
    {
        self::$statuses = null;
        Cache::forget(self::DB_CACHE_KEY);
        Cache::forget(self::OLD_DB_CACHE_KEY);

        // Also clear individual module caches set by old code
        $modules = self::getAllModuleNames();
        foreach ($modules as $moduleName) {
            Cache::forget('module_'.$moduleName);
        }
    }

    /**
     * Force a refresh on next access (useful in tests / after module changes).
     */
    public static function reset(): void
    {
        self::$statuses = null;
    }

    /**
     * Boot: load all module names from file cache (or DB), then compute statuses.
     * This runs exactly once per request lifecycle.
     */
    private static function boot(): void
    {
        self::$statuses = [];

        try {
            $moduleNames = self::getAllModuleNames();

            foreach ($moduleNames as $moduleName) {
                self::$statuses[$moduleName] = self::computeStatus($moduleName);
            }
        } catch (Throwable $e) {
            // If anything fails, return empty (all modules inactive) — never crash the app
            self::$statuses = [];
        }
    }

    /**
     * Get all module names from file cache or DB.
     * Cached for 10 minutes — survives across requests.
     *
     * @return string[]
     */
    private static function getAllModuleNames(): array
    {
        return array_keys(self::getModuleRecords());
    }

    /**
     * Get all module records needed for status checks in one cached DB read.
     *
     * @return array<string, array{purchase_code: string|null}>
     */
    private static function getModuleRecords(): array
    {
        return Cache::remember(self::DB_CACHE_KEY, self::DB_CACHE_TTL, function (): array {
            return InfixModuleManager::select('name', 'purchase_code')
                ->get()
                ->mapWithKeys(function (InfixModuleManager $module): array {
                    return [
                        $module->name => [
                            'purchase_code' => $module->purchase_code,
                        ],
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Compute the actual status for one module.
     * This is the logic extracted from the old moduleStatusCheck() helper,
     * now called exactly once per module per request.
     */
    private static function computeStatus(string $moduleName): bool
    {
        try {
            // 1. Check if module exists in DB list
            $moduleRecords = self::getModuleRecords();
            $moduleRecord = $moduleRecords[$moduleName] ?? null;
            if (! $moduleRecord) {
                return false;
            }

            // 2. Check if module provider file exists and module is not disabled
            $providerPath = module_path($moduleName, 'Providers/'.$moduleName.'ServiceProvider.php');
            if (file_exists($providerPath)) {
                $moduleInstance = Module::find($moduleName);
                if ($moduleInstance && $moduleInstance->isDisabled()) {
                    return false;
                }
            }

            // 3. Check purchase code from the batched module registry cache.
            if (empty($moduleRecord['purchase_code'])) {
                // Fallback: allow locally-installed custom modules enabled in modules_statuses.json
                $modulesStatuses = self::getModulesStatuses();
                if (! ($modulesStatuses[$moduleName] ?? false)) {
                    return false;
                }
            }

            // 4. Subscription check (if applicable)
            if ($moduleName !== 'Saas' && isSubscriptionEnabled()) {
                $planMods = planPermissions('modules') ?? [];
                if (in_array($moduleName, $planMods, true)) {
                    return isModuleForSchool($moduleName);
                }
            }

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Read modules_statuses.json (nwidart's module enable/disable file).
     * Returns an associative array of [ ModuleName => bool ].
     */
    private static function getModulesStatuses(): array
    {
        $path = base_path('modules_statuses.json');
        if (! file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }
}
