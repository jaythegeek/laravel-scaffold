<?php

namespace JayTheGeek\LaravelScaffolding;

use Illuminate\Support\ServiceProvider;
use JayTheGeek\LaravelScaffolding\Commands\BuildModel;
use JayTheGeek\LaravelScaffolding\Commands\ScaffoldPasswordless;

class LaravelScaffoldingServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'jaythegeek');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'jaythegeek');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravelscaffolding.php', 'laravelscaffolding');

        // Register the service the package provides.
        $this->app->singleton('laravelscaffolding', function ($app) {
            return new LaravelScaffolding;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelscaffolding'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/laravelscaffolding.php' => config_path('laravelscaffolding.php'),
        ], 'laravelscaffolding.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/jaythegeek'),
        ], 'laravelscaffolding.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/jaythegeek'),
        ], 'laravelscaffolding.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/jaythegeek'),
        ], 'laravelscaffolding.views');*/

        // Registering package commands.
        $this->commands([
            ScaffoldPasswordless::class,
            BuildModel::class
        ]);
    }
}
