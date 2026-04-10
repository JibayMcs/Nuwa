<?php

namespace JibayMcs\Nuwa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JibayMcs\Nuwa\Nuwa
 */
class Nuwa extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JibayMcs\Nuwa\Nuwa::class;
    }
}
