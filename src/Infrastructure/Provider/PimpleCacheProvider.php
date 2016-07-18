<?php

namespace Cmp\Cache\Infrastructure\Provider;

use Closure;
use Cmp\Cache\Application\CacheFactory;
use InvalidArgumentException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Redis;

/**
 * Class PimpleCacheProvider
 *
 * @package Cmp\Cache\Infrastructure\Provider
 */
class PimpleCacheProvider implements ServiceProviderInterface
{
    /**
     * Registers cache services on the given container.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['cache.backend'] = 'array';
        $pimple['cache.debug']   = false;

        $pimple['cache'] = function () use ($pimple) {
            $cache = $this->getCache($pimple['cache.backend']);

            if ($pimple['cache.debug']) {
                $cache = CacheFactory::decorateForTesting($cache);
            }

            return $cache;
        };
    }

    /**
     * Gets a cache object based on the backend requested
     *
     * @param $backend
     *
     * @return Closure|\Cmp\Cache\Infrastructure\ArrayCache
     */
    private function getCache($backend)
    {
        if (is_array($backend) && isset($backend['redis'])) {
            return $this->getRedis($backend['redis']);
        }

        if ($backend == 'array') {
            return CacheFactory::arrayCache();
        }

        throw new InvalidArgumentException("Invalid cache backend");
    }

    /**
     * Return a factory closure to build
     *
     * @param array|Redis $redis
     *
     * @return Closure
     */
    private function getRedis($redis)
    {
        if ($redis instanceof Redis) {
            return CacheFactory::redisCache($redis);
        }

        $host    = isset($redis['host']) ? $redis['host'] : '127.0.0.1';
        $port    = isset($redis['port']) ? $redis['port'] : 6379;
        $db      = isset($redis['db']) ? $redis['db'] : 0;
        $timeout = isset($redis['timeout']) ? $redis['timeout'] : 0.0;

        return CacheFactory::redisFromParams($host, $port, $db, $timeout);
    }
}
