<?php

namespace JayTheGeek\LaravelScaffold\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelScaffold extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-scaffold';
    }
}
