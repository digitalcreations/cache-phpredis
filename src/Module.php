<?php

namespace DC\Cache\Implementations\Redis;

/**
 * Redis module for caching.
 */
class Module extends \DC\IoC\Modules\Module
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct($connectionOrHost, $port = 6379)
    {
        parent::__construct("dc/cache", []);
        $this->cache = new Cache($connectionOrHost, $port);
    }

    /**
     * @return \DC\Cache\ICache
     */
    function getInstance() {
        return $this->cache;
    }

    function register(\DC\IoC\Container $container) {
        $container->register($this->cache)->to('\DC\Cache\ICache');
    }

    function createContainer() {
        $container = new \DC\IoC\Container($this->getInstance());
        $container->registerModules([$this]);
        return $container;
    }
}