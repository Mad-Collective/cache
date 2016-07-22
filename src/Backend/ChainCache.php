<?php

namespace Cmp\Cache\Backend;

use Cmp\Cache\Cache;
use Cmp\Cache\Exceptions\NotFoundException;
use Cmp\Cache\TimeToLiveAwareCache;
use Cmp\Cache\Traits\MultiCacheTrait;

/**
 * Class ArrayCache
 * 
 * A simple backend powered by an array in memory
 *
 * @package Cmp\Cache\Infrastureture\Backend
 */
class ChainCache implements Cache
{
    use MultiCacheTrait;

    /**
     * Stored items
     * 
     * @var Cache[]
     */
    private $cache = [];

    /**
     * Pushes a cache in the chain
     *
     * @param Cache $cache
     *
     * @return $this
     */
    public function pushCache(Cache $cache)
    {
        $this->cache[] = $cache;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $item, $timeToLive = null)
    {
        $success = true;
        foreach ($this->cache as $cache) {
            $success = $success && $cache->set($key, $item, $timeToLive);
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        foreach ($this->cache as $cache) {
            if ($cache->has($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        foreach ($this->cache as $index => $cache) {
            $item = $cache->get($key);
            if ($item) {
                $this->populatePreviousCaches($index, $key, $item, $cache->getTimeToLive($key));

                return $item;
            }
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function demand($key)
    {
        $item = $this->get($key);
        if (!$item) {
            throw new NotFoundException($key);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $success = true;
        foreach ($this->cache as $cache) {
            $success = $success && $cache->delete($key);
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $success = true;
        foreach ($this->cache as $cache) {
            $success = $success && $cache->flush();
        }

        return $success;
    }

    /**
     * Gets the remaining time to live for an item
     *
     * @param $key
     *
     * @return int|null
     */
    public function getTimeToLive($key)
    {
        foreach ($this->cache as $cache) {
            $timeToLive = $cache->getTimeToLive($key);
            if ($timeToLive) {
                return $timeToLive;
            }
        }

        return null;
    }

    /**
     * @param string   $index
     * @param string   $key
     * @param string   $item
     * @param int|null $timeToLive
     */
    private function populatePreviousCaches($index, $key, $item, $timeToLive)
    {
        for (--$index; $index >= 0 ; $index--) {
            $this->cache[$index]->set($key, $item, $timeToLive);
        }
    }
}
