# Pluggit - Cache

[![Build Status](https://travis-ci.org/CMProductions/cache.svg?branch=master)](https://travis-ci.org/CMProductions/cache)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/CMProductions/cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/CMProductions/cache/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/CMProductions/cache/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/CMProductions/cache/?branch=master)

## TLDR;
```php
/** @var \Cmp\Cache\Cache $cache */
$cache = (new CacheBuilder)
  ->withLogging($psrLogger)
  ->withoutExceptions()
  ->withArrayCache()
  ->withRedisFromParams($host, $port, $dbNumber)
  ->build();

// Set an item
$cache->set('foo', 'bar');

// Demand an item, throws an exception if not present
$bar = $cache->demand('foo');

// Get an item, if not present in any cache it will return the given default
$default = $cache->get('not found', 'default');

// Delete an item
$cache->delete('foo');

// Empty the cache
$cache->flush();
```

## The Cache interface

The cache interface allows access to 9 methods:
* `set`
* `setAll`
* `has`
* `get`
* `getAll`
* `demand`
* `delete`
* `deleteAll`
* `flush`

### Set
Use it to store values in the cache. You can make an item expire after a certain time by passing 
the `$timeToLive` parameter bigger than zero

__*Note:*__ Time to live must be an integer representing seconds

### SetAll
Use it to store multiple values at one

### Has
Checks if an item is in the cache and has not expired

### Get
Tries to get an item from the cache, and if it's not there, it return a given default value

### GetAll
Tries to get multiple items from the cache, if an item is not found, the key will be returned with a null 

### Demand
Tries to retrieve an item from the cache, if it's not present, it throws a `NotFoundException`

### Delete
Deletes an item from the cache

### Delete All
Deletes multiple items at once

### Flush
It empties the cache

## Cache backends provided
These are the current backend implementations:
* Redis
* Array (in memory)
* ChainCache

__*Note:*__ You can use the `CacheFactory` to easily build a cache object with one of the provided backends

### ChainCache
The chain cache will accept one or more cache and tries all caches before failing

## Cache backend decorator provided
* LoggerCache

### Logger Cache
It allows to log any exception thrown from the decorated cache. You can choose the silent mode to avoid throwing exceptions
from the cache

**Note:** This decorator is used always when building a cache trough the builder, as it ensures that all exceptions thrown extend the base CacheException

## Pimple service provider
The library includes a service provider for Pimple ^3.0 (included on Silex ^2.0) to easily register a cache

By default it will register an `ArrayCache` on the key `cache`
```php
$pimple->register(new CacheServiceProvider());

/** @var LoggerCache $cache */
$cache = $pimple['cache'];

/** @var ArrayCache $cache */
$arrayCache = $pimple['cache']->getDecoratedCache();
```
### Options 
* **_cache.backends_**: an array of backends caches to use, if more than 
one is provided, the ChainCache will be used to wrap them all. Each backend option **must** have the key _backend_, the available options are: 
  * `array`: It will create an ArrayCache (same as default)
  * `redis`: If you already have a redis connection, it can be passed on the key _connection_ (if a string is used, the provider will try to get the service from the container.
  If the _connection_ is not given, the provider will try to build a connection reading from the options array:
     * _host:_ 127.0.0.1 by default
     * _port:_ 6379 by default
     * _db:_ 0 by default
     * _timeout:_ 0.0 by default
  * `string`: If a string is given, the provider will try to get the service from the container, the service must implement then `Cmp\Cache\Cache` interface
  * `Cmp\Cache\Cache`: If an object implementing the Cache interface is given, it will be used directly

* **_cache.exceptions_**: a boolean, true by default. If is set to true, the cache will be throw exceptions, if false, no exceptions will be thrown.
* **_cache.logging_**: It allows to log exceptions thrown by the cache system. it accepts an array, with the following options:
  * `logger`: And instance of an logger implementing the Psr\LoggerInterface
  * `log_level`: The log level to use for logging the exceptions, 'alert' by default. Must be one of the valid levels defined at Psr\LogLevel

__Examples:__
```php
// Redis from parameters
$pimple->register(new PimpleCacheProvider(), ['cache.backends' => [
    ['backend' => 'redis', 'host' => '127.0.0.1', 'port' => 6379, 'db' => 1, 'timeout' => 2.5]
]]);

// ArrayCache + Redis from existing connection
$pimple->register(new PimpleCacheProvider(), ['cache.backends' => [
    ['backend' => 'array']
    ['backend' => 'redis', 'connection' => $redisConnection,]
]]);

// ArrayCache + Redis from a service registered in the container
$pimple->register(new PimpleCacheProvider(), ['cache.backends' => [
    ['backend' => 'array']
    ['backend' => 'redis', 'connection' => 'redis.connection']
]]);

// Chain caches with logging and muted exceptions
$pimple->register(new PimpleCacheProvider(), [
    'cache.backends'   => [
        ['backend' => 'array'],
        ['backend' => 'custom_cache_service_key'],
    ],
    'cache.exceptions' => false,
    'cache.logging'    => ['logger' => $psrLogger, 'log_level' => PsrLevel::CRITICAL],
]);
```