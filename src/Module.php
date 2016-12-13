<?php

namespace DC\Cache\Implementations\Redis;

/**
 * Redis module for caching.
 */
class Module extends \DC\IoC\Modules\Module
{
    public function __construct()
    {
        parent::__construct("dc/cache", []);
    }

    function register(\DC\IoC\Container $container) {
        $container->register('\DC\Cache\Implementations\Redis\Cache')->to('\DC\Cache\ICache')->withContainerLifetime();
    }
}