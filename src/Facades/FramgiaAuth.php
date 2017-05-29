<?php

namespace Framgia\Education\Auth\Facades;

use Illuminate\Support\Facades\Facade;
use Framgia\Education\Auth\Contracts\Factory;

/**
 * @see \Framgia\Education\Auth\FramgiaAuthManager
 */
class FramgiaAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
