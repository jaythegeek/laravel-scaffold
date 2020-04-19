<?php

namespace LaravelScaffold\ResourceGenerator;

use Illuminate\Support\ServiceProvider;

class ResourceGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.laravel-scaffold.makeresource', function ($app) {
            return $app['LaravelScaffold\ResourceGenerator\Commands\ResourceMakeCommand'];
        });
        $this->commands('command.laravel-scaffold.makeresource');


        // if ($this->app->runningInConsole()) {
        //     $this->commands([
        //         FooCommand::class,
        //         BarCommand::class,
        //     ]);
        // }

    }
}
