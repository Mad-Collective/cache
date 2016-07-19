# Pluggit - Cache

[![Build Status](https://travis-ci.org/CMProductions/cache.svg?branch=master)](https://travis-ci.org/CMProductions/cache)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/CMProductions/cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/CMProductions/cache/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/CMProductions/cache/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/CMProductions/cache/?branch=master)

## TLDR;
```php
/** @var \Cmp\Cache\Domain\Cache $cache */
$cache = CacheFactory::redisFromParams($host, $port, $dbNumber);

if ($debug) {
    $cache = CacheFactory::decorateForTesting($cache);
}

// Set an item
$cache->set('foo', 'bar');

// Demand an item, throws an exception if not present
$bar = $cache->demand('foo');

// Get an item, if not present it will return the given default
$default = $cache->get('not found', 'default');

// Delete an item
$cache->delete('foo');

// Empty the cache
$cache->flush();
```

## The Cache interface

The cache interface allows access to 6 methods:
* `set`
* `has`
* `get`
* `demand`
* `delete`
* `flush`

### Set
Use it to store values in the cache. You can make an item expire after a certain time by passing 
the `$timeToLive` parameter bigger than zero

__*Note:*__ Time to live must be an integer representing seconds

### Has
Checks if an item is in the cache and has not expired

### Get
Tries to get an item from the cache, and if it's not there, it return a given default value

### Demand
Tries to retrieve an item from the cache, if it's not present, it throws a `NotFoundException`

### Delete
Deletes an item from the cache

### Flush
It empties the cache

## Cache backends provided
These are the current backend implementations:
* Redis
* Array (in memory)

__*Note:*__ You can use the `CacheFactory` to easily build a cache object with one of the provided backends

## Cache backend Decorator

### Test
It allows to make assertions on the calls executed over a backend

## Pimple service provider
The library includes a service provider for Pimple ^3.0 (included on Silex ^2.0) to easily register a cache

By default it will register an `ArrayCache` on the key `cache`
```php
$pimple->register(new PimpleCacheProvider(), $options);

/** @var ArrayCache $cache */
$cache = $pimple['cache'];
```
### Options 
* **_cache.backend_**: accepted values are:
  * `array`: It will create an ArrayCache (same as default)
  * `redis`: An array where the value must be an existing `\Redis` connection or an array with the following configuration to built it:
     * _host:_ 127.0.0.1 by default
     * _port:_ 6379 by default
     * _db:_ 0 by default
     * _timeout:_ 0.0 by default  

* **_cache.debug_**: a boolean, false by default. If is set to true, the cache will be wrapped in the `TestDecorator`

__Examples:__
```php
// Redis from parameters
$pimple->register(new PimpleCacheProvider(), ['cache.backend' => ['redis' => [
    'host'    => '127.0.0.1', 
    'port'    => 6379,
    'db'      => 1,
    'timeout' => 2.5,
]]]);

// Redis from an existing connection
$redis = new \Redis();
$pimple->register(new PimpleCacheProvider(), ['cache.backend' => ['redis' => $redis]]);

// An ArrayCache wrapped with the TestDecorator
$pimple->register(new PimpleCacheProvider(), [
    'cache.backend' => 'array',
    'cache.debug'   => true
]);
```
