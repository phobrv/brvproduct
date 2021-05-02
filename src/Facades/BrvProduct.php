<?php

namespace Phobrv\BrvProduct\Facades;

use Illuminate\Support\Facades\Facade;

class BrvProduct extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'brvproduct';
    }
}
