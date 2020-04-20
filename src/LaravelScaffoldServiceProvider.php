<?php

namespace JayTheGeek\LaravelScaffold;

use Illuminate\Support\ServiceProvider;
use JayTheGeek\LaravelScaffold\Commands\BuildModel;
use JayTheGeek\LaravelScaffold\Commands\ScaffoldPasswordless;

class LaravelScaffoldServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
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
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-scaffold.php', 'laravel-scaffold');

        // Register the service the package provides.
        $this->app->singleton('laravel-scaffold', function ($app) {
            return new LaravelScaffold;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-scaffold'];
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
            __DIR__ . '/../config/laravel-scaffold.php' => config_path('laravel-scaffold.php'),
        ], 'laravel-scaffold.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/jaythegeek'),
        ], 'laravel-scaffold.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/jaythegeek'),
        ], 'laravel-scaffold.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/jaythegeek'),
        ], 'laravel-scaffold.views');*/

        // Registering package commands.
        $this->commands([
            ScaffoldPasswordless::class,
            BuildModel::class
        ]);
    }
}
