<?php

namespace TobMoeller\LaravelMailAllowlist\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TobMoeller\LaravelMailAllowlist\LaravelMailAllowlist
 */
class LaravelMailAllowlist extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TobMoeller\LaravelMailAllowlist\LaravelMailAllowlist::class;
    }
}
