![DC\Cache - Caching interface](logo.png)

## Installation

```
$ composer require dc/cache-phpredis
```

Or add it to `composer.json`:

```json
"require": {
	"dc/cache-phpredis": "0.*",
}
```

```
$ composer install
```

## Getting started

Provide either a `\Redis` connection or a hostname and port.

```php
$cache = new \DC\Cache\Implementations\Redis\Cache('127.0.0.1', 6379);
// or
$redis = new \Redis();
$redis->connect('127.0.0.1');
$cache = new \DC\Cache\Implementations\Redis\Cache($redis);
```

Otherwise, use it according [to the interface](http://github.com/digitalcreations/cache).