<?php
namespace Cmp\Cache\Factory;

use Cmp\Cache\Backend\ChainCache;
use Cmp\Cache\Backend\ArrayCache;
use Cmp\Cache\Backend\NullCache;
use Cmp\Cache\Backend\RedisCache;
use Cmp\Cache\Backend\RedisClusterCache;
use Cmp\Cache\Cache;
use Cmp\Cache\Decorator\LoggerCache;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Redis;
use RedisCluster;

/**
 * Class CacheFactory
 *
 * @package Cmp\Cache\Application
 */
class CacheFactory implements CacheFactoryInterface
{
    /**
     * Builds an array cache
     * 
     * @return ArrayCache
     */
    public function arrayCache()
    {
        return new ArrayCache();
    }

    /**
     * Builds a null (noop) cache
     *
     * @return NullCache
     */
    public function nullCache()
    {
        return new NullCache();
    }

    /**
     * {@inheritdoc}
     */
    public function redisFromParams($host = '127.0.0.1', $port = 6379, $db = 0, $timeOut = 0.0)
    {
        $redis = new Redis();
        $redis->pconnect($host, $port, $timeOut);
        $redis->select($db);

        return self::redisCache($redis);
    }

    /**
     * {@inheritdoc}
     */
    public function redisCache(Redis $redis)
    {
        return new RedisCache($redis);
    }

    /**
     * {@inheritdoc}
     */
    public function redisClusterCache(RedisCluster $redis)
    {
        return new RedisClusterCache($redis);
    }

    /**
     * {@inheritdoc}
     */
    public function chainCache(array $caches = [])
    {
        $chain = new ChainCache();
        foreach ($caches as $cache) {
            $chain->pushCache($cache);
        }

        return $chain;
    }

    /**
     * {@inheritdoc}
     */
    public function loggerCache(
        Cache $cache,
        $withExceptions = true,
        LoggerInterface $logger = null,
        $logLevel = LogLevel::ERROR
    ) {
        return new LoggerCache($cache, $withExceptions, $logger, $logLevel);
    }
}
