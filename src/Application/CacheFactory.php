<?php

namespace Cmp\Cache\Application;

use Cmp\Cache\Domain\Cache;
use Cmp\Cache\Infrastructure\ArrayCache;
use Cmp\Cache\Infrastructure\RedisCache;
use Redis;

/**
 * Class CacheFactory
 *
 * @package Cmp\Cache\Application
 */
class CacheFactory
{
    /**
     * @return ArrayCache
     */
    public static function arrayCache()
    {
        return new ArrayCache();
    }

    /**
     * @param string $host
     * @param int    $port
     * @param int    $db
     * @param float  $timeOut
     *
     * @return RedisCache
     */
    public static function redisFromParams($host = '127.0.0.1', $port = 6379, $db = 0, $timeOut = 0.0)
    {
        $redis = new Redis();
        $redis->pconnect($host, $port, $timeOut);
        $redis->select($db);

        return self::redisCache($redis);
    }

    /**
     * @param Redis $redis
     *
     * @return RedisCache
     */
    public static function redisCache(Redis $redis)
    {
        return new RedisCache($redis);
    }

    /**
     * @param Cache $cache
     *
     * @return TestCacheDecorator
     */
    public static function decorateForTesting(Cache $cache)
    {
        return new TestCacheDecorator($cache);
    }
}
