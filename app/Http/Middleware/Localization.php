<?php

namespace App\Http\Middleware;

use App\Support\ModuleRegistry;
use Carbon\Carbon;
use Closure;
use File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Localization
{
    /**
     * Translation cache TTL: 6 hours.
     *
     * CRITICAL FIX: The original code called Cache::forget('translations') then
     * Cache::remember('translations') on EVERY request — meaning the cache was
     * destroyed and rebuilt every single page load. It never served cached data.
     *
     * Fix: Removed Cache::forget. The cache now persists for 6 hours per locale,
     * and is keyed by locale so switching language doesn't invalidate other locales.
     */
    private const TRANSLATION_CACHE_TTL_HOURS = 6;

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if (Storage::exists('.app_installed') && Storage::get('.app_installed')) {
            $locale = getUserLanguage();
            App::setLocale($locale);

            // Locale-scoped cache key — switching language loads its own cache entry
            $cacheKey = 'translations_'.$locale;
            Cache::remember($cacheKey, Carbon::now()->addHours(self::TRANSLATION_CACHE_TTL_HOURS), function () {
                return $this->getTranslations();
            });

            // Timezone from generalSetting() — which itself uses Cache::remember (see Helper.php fix)
            $generalSettings = generalSetting();
            $timeZone = $generalSettings->timeZone->time_zone ?? 'Asia/Dhaka';
            date_default_timezone_set($timeZone);
        }

        return $next($request);
    }

    /**
     * Build translation arrays for all active locales.
     * Only called on cache miss (once per 6-hour window).
     */
    public function getTranslations()
    {
        $locales = ['en'];
        $userLang = getUserLanguage();

        if ($userLang !== null && $userLang !== 'en') {
            $locales[] = $userLang;
        }

        $translations = [];
        foreach ($locales as $locale) {
            $translations[$locale] = [
                'json' => $this->jsonTranslations($locale),
                'php' => $this->phpTranslations($locale),
            ];
        }

        return collect($translations);
    }

    /**
     * Collect translations from core + active module lang files.
     *
     * FIX: Uses ModuleRegistry::activeModules() instead of looping all modules
     * and calling moduleStatusCheck() on each (which was a DB + filesystem hit per module).
     */
    private function jsonTranslations(?string $locale): string
    {
        if ($locale === null) {
            $locale = 'en';
        }
        $files = glob(resource_path('lang/'.$locale.'/*.php')) ?: [];

        foreach (ModuleRegistry::activeModules() as $moduleName) {
            $moduleFiles = glob(module_path($moduleName).'/Resources/lang/'.$locale.'/*.php') ?: [];
            $files = array_merge($files, $moduleFiles);
        }

        $lang = [];
        foreach ($files as $file) {
            $fileName = basename($file, '.php');
            if ($fileName !== 'lang' && file_exists($file) && is_array(include $file)) {
                $lang = array_merge($lang, include $file);
            }
        }

        return json_encode($lang, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Load PHP translation arrays for a given locale.
     */
    private function phpTranslations(string $locale)
    {
        $path = resource_path('lang/'.$locale);

        if (! file_exists($path)) {
            return null;
        }

        return collect(File::allFiles($path))
            ->flatMap(fn ($file) => [$file->getBasename('.php') => trans($file->getBasename('.php'), [], $locale)])
            ;
    }
}
