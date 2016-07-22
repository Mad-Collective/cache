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