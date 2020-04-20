<?php

namespace JayTheGeek\LaravelScaffolding\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelScaffolding extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelscaffolding';
    }
}
