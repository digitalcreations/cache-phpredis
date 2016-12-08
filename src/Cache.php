<?php

namespace DC\Cache\Implementations\Redis;

class Cache implements \DC\Cache\ICache {

    /**
     * @var \Redis
     */
    private $connection;
    public function __construct($connectionOrHost, $port = 6379)
    {
        if ($connectionOrHost instanceof \Redis) {
            $this->connection = $connectionOrHost;
        }
        else {
            $this->connection = new \Redis();
            $this->connection->connect($connectionOrHost, $port);
            $this->connection->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        }
    }

    /**
     * @inheritdoc
     */
    function get($key)
    {
        return $this->connection->get($key);
    }

    /**
     * Set an item in the cache
     *
     * @param string $key
     * @param mixed $value
     * @param int|\DateInterval|\DateTime $validity Number of seconds this is valid for (if int)
     * @return void
     */
    function set($key, $value, $validity = null)
    {
        $this->connection->set($key, $value);

        if ($validity instanceof \DateInterval) {
            $validity = (new \DateTimeImmutable())->add($validity);
        }

        if ($validity instanceof \DateTimeInterface) {
            $this->connection->expireat($key, $validity->getTimestamp());
        }
        else if (is_numeric($validity)) {
            $this->connection->expire($key, $validity);
        }
        else if ($validity != null) {
            throw new \UnexpectedValueException("Unexpected validity");
        }
    }

    /**
     * Try to get an item, and if missed call the fallback method to produce the value and store it.
     *
     * @param string $key
     * @param callable $fallback
     * @param int|\DateInterval|\DateTime|callback $validity Number of seconds this is valid for (if int)
     * @return mixed
     */
    function getWithFallback($key, callable $fallback, $validity = null)
    {
        $value = $this->connection->get($key);
        if (!$value && !$this->connection->exists($key)) {
            $value = $fallback();
            if (is_callable($validity)) {
                $validity = call_user_func($validity, $value);
            }

            $this->set($key, $value, $validity);
            return $value;
        }
        return $value;
    }

    /**
     * Remove a key from the cache.
     *
     * @param string $key
     * @return void
     */
    function delete($key)
    {
        $this->connection->del($key);
    }

    /**
     * Remove all items from the cache (flush it).
     *
     * @return void
     */
    function deleteAll()
    {
        $this->connection->flushAll();
    }

    /**
     * Return an iterable list of all the keys in the cache.
     *
     * It is recommended that this list is iterated using a foreach, since that will allow the garbage collector
     * to reclaim memory as we go along.
     *
     * There is no guarantee as to the ordering of the keys.
     *
     * @return array
     */
    function getAllKeys()
    {
        return $this->connection->keys("*");
    }
}