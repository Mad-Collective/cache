<?php

namespace Cmp\Cache\Factory\Pimple;

use Cmp\Cache\Backend\RedisCache;
use Cmp\Cache\Cache;
use Cmp\Cache\Factory\CacheBuilder;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Redis;

/**
 * Class CacheServiceProvider
 *
 * @package Cmp\Cache\Factory\Pimple
 */
class CacheServiceProvider implements ServiceProviderInterface
{
    /**
     * @var CacheBuilder
     */
    private $builder;

    /**
     * PimpleCacheProvider constructor.
     *
     * @param CacheBuilder|null $builder
     */
    public function __construct(CacheBuilder $builder = null)
    {
        $this->builder = $builder ?: new CacheBuilder();
    }

    /**
     * Registers cache services on the given container.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['cache.backends']   = ['array' => ['backend' => 'array']];
        $pimple['cache.logging']    = ['logger' => null, 'level' => LogLevel::ALERT];
        $pimple['cache.exceptions'] = true;

        $pimple['cache'] = function () use ($pimple) {
            return $this->build($pimple);
        };
    }

    /**
     * @param Container $pimple
     *
     * @return Cache
     */
    private function build(Container $pimple)
    {
        if (!$pimple['cache.exceptions']) {
            $this->builder->withoutExceptions();
        }

        if ($pimple['cache.logging']['logger'] instanceof LoggerInterface) {
            $this->builder->withLogging($pimple['cache.logging']['logger'], $pimple['cache.logging']['level']);
        }

        foreach ($pimple['cache.backends'] as $backend) {
            $this->addBackend($pimple, $backend['backend'], $backend);
        }

        return $this->builder->build();
    }

    /**
     * @param Container $pimple
     * @param string    $backend
     * @param array     $options
     *
     * @return $this|CacheServiceProvider
     */
    private function addBackend(Container $pimple, $backend, array $options)
    {
        if ($backend == 'array') {
            return $this->builder->withArrayCache();
        }

        if ($backend == 'redis') {
            return $this->buildRedis($pimple, $options);
        }

        if ($backend instanceof Cache) {
            return $this->builder->withCache($backend);
        }

        return $this->builder->withCache($pimple[$backend]);
    }

    /**
     * @param Container $pimple
     * @param array     $options
     *
     * @return $this
     */
    private function buildRedis(Container $pimple, array $options)
    {
        if (!isset($options['connection'])) {
            return $this->builder->withRedisCacheFromParams(
                $this->getParameter($options, 'host', RedisCache::DEFAULT_HOST),
                $this->getParameter($options, 'port', RedisCache::DEFAULT_HOST),
                $this->getParameter($options, 'db', RedisCache::DEFAULT_DB),
                $this->getParameter($options, 'timeout', RedisCache::DEFAULT_TIMEOUT)
            );
        }

        if ($options['connection'] instanceof Redis) {
            return $this->builder->withRedis($options['connection']);
        }

        return $this->builder->withRedis($pimple[$options['connection']]);
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
