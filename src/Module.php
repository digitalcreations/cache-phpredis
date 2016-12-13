<?php

namespace DC\Cache\Implementations\Redis;

/**
 * Redis module for caching.
 */
class Module extends \DC\IoC\Modules\Module
{
    private $connectionOrHost;
    /**
     * @var int
     */
    private $port;

    public function __construct($connectionOrHost, $port = 6379)
    {
        parent::__construct("dc/cache", []);
        $this->connectionOrHost = $connectionOrHost;
        $this->port = $port;
    }

    function register(\DC\IoC\Container $container) {
        $container->register(function() {
            return new Cache($this->connectionOrHost, $this->port);
        })->to('\DC\Cache\ICache')->withContainerLifetime();
    }
}