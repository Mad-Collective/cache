<?php

namespace Cmp\Cache\Application;

use Cmp\Cache\Domain\Cache;

/**
 * Class CacheDecorator
 *
 * @package Cmp\Cache\Infrastureture
 */
abstract class CacheDecorator implements Cache
{
    /**
     * The decorated cache
     *
     * @var Cache
     */
    private $cache;

    /**
     * CacheDecorator constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeToLive = 0)
    {
        $this->cache->set($key, $value, $timeToLive);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->cache->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->cache->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function demand($key)
    {
        return $this->cache->demand($key);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->cache->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->cache->flush();
    }
}
