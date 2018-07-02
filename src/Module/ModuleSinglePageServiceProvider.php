<?php

namespace Noking50\Modules\SinglePage;

use Illuminate\Support\ServiceProvider;
use Noking50\Modules\SinglePage\Services\ControllerOutputService;

class ModuleSinglePageServiceProvider extends ServiceProvider {

    public function boot() {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'module_single_page');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../views', 'module_single_page');
        $this->publishes([
            __DIR__ . '/../config/module_single_page.php' => config_path('module_single_page.php'),
            __DIR__ . '/../lang' => resource_path('lang/vendor/module_single_page'),
            __DIR__.'/../views' => base_path('resources/views/vendor/module_single_page'),
            __DIR__.'/../enum/module_single_page-content_type.php' => base_path('resources/enum/module_single_page-content_type.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->mergeConfigFrom(
                __DIR__ . '/../config/module_single_page.php', 'module_single_page'
        );
        $this->app->singleton('module_single_page', function ($app) {
            return new ControllerOutputService(
                    $app['Noking50\Modules\SinglePage\Services\ModuleSinglePageService'], 
                    $app['Noking50\Modules\Required\Services\LanguageService'], 
                    $app['Noking50\Modules\SinglePage\Validations\ModuleSinglePageValidation']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return ['module_single_page'];
    }

}
