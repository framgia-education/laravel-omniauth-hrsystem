<?php

namespace Framgia\Education\Auth\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param  string  $driver
     * @return \Framgia\Education\Auth\Contracts\Provider
     */
    public function driver($driver = null);
}
