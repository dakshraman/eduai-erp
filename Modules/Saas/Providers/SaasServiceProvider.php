<?php

namespace Modules\Saas\Providers;

use Config;
use Illuminate\Support\ServiceProvider;

class SaasServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Saas';

    /**
     * @var string
     */
    protected $moduleNameLower = 'saas';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Saas', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/saas');

        $sourcePath = module_path('Saas', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function (string $path): string {
            return $path.'/modules/saas';
        }, Config::get('view.paths')), [$sourcePath]), 'saas');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/saas');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'saas');
        } else {
            $this->loadTranslationsFrom(module_path('Saas', 'Resources/lang'), 'saas');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Saas', 'Config/config.php') => config_path('saas.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Saas', 'Config/config.php'), 'saas'
        );
    }
}
