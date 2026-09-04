<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * CacheInvalidationHelper
 *
 * Provides methods to clear optimized caches without needing artisan CLI.
 * For use in admin controllers when settings are changed.
 *
 * USAGE IN CONTROLLERS:
 *   // After saving general settings:
 *   CacheInvalidationHelper::clearGeneralSettings($schoolId);
 *
 *   // After installing/uninstalling a module:
 *   CacheInvalidationHelper::clearModuleRegistry();
 *
 *   // Full cache flush (after major system changes):
 *   CacheInvalidationHelper::clearAll();
 */
class CacheInvalidationHelper
{
    /**
     * Clear cached general settings for a specific school.
     * Call this after saving SmGeneralSettings.
     */
    public static function clearGeneralSettings(int $schoolId): void
    {
        Cache::forget('general_settings_'.$schoolId);
        Cache::forget('system_date_format_'.$schoolId);

        Log::info("Cache cleared: general_settings for school #{$schoolId}");
    }

    /**
     * Clear the module registry cache.
     * Call this after installing, uninstalling, enabling, or disabling a module.
     */
    public static function clearModuleRegistry(): void
    {
        ModuleRegistry::invalidate();

        Log::info('Cache cleared: module registry');
    }

    /**
     * Clear translation caches for all locales.
     * Call this after updating language files or adding a module with translations.
     */
    public static function clearTranslations(): void
    {
        // Clear locale-scoped translation caches
        foreach (['en', 'bn', 'ar', 'fr', 'es', 'be', 'indo', 'ca'] as $locale) {
            Cache::forget('translations_'.$locale);
        }

        Log::info('Cache cleared: translations');
    }

    /**
     * Clear maintenance mode cache.
     * Call this after toggling maintenance mode.
     */
    public static function clearMaintenanceSetting(): void
    {
        Cache::forget('maintenance_setting');
    }

    /**
     * Clear all application-level caches.
     * Use sparingly — this clears everything.
     */
    public static function clearAll(): void
    {
        self::clearModuleRegistry();
        self::clearTranslations();
        self::clearMaintenanceSetting();

        // Clear all school settings (flush entire file cache)
        Cache::flush();

        Log::info('Cache cleared: full flush');
    }
}
