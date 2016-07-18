<?php

namespace Cmp\Cache\Infrastructure\Provider;

use Closure;
use Cmp\Cache\Application\CacheFactory;
use Cmp\Cache\Infrastructure\RedisCache;
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
     * @return \Cmp\Cache\Domain\Cache
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
     * @return \Cmp\Cache\Domain\Cache
     */
    private function getRedis($redis)
    {
        if ($redis instanceof Redis) {
            return CacheFactory::redisCache($redis);
        }

        $host    = $this->getParameter($redis, 'host', RedisCache::DEFAULT_HOST);
        $port    = $this->getParameter($redis, 'port', RedisCache::DEFAULT_PORT);
        $db      = $this->getParameter($redis, 'db', RedisCache::DEFAULT_DB);
        $timeout = $this->getParameter($redis, 'timeout', RedisCache::DEFAULT_TIMEOUT);

        return CacheFactory::redisFromParams($host, $port, $db, $timeout);
    }

    /**
     * @param array  $options
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    private function getParameter(array $options, $key, $default)
    {
        return isset($options[$key]) ? $options[$key] : $default;
    }
}
