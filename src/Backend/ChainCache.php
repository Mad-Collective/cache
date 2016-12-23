<?php

namespace Cmp\Cache\Backend;

use Cmp\Cache\Cache;
use Cmp\Cache\Exceptions\NotFoundException;
use Cmp\Cache\Traits\MultiCacheTrait;

/**
 * Class ArrayCache
 * 
 * A simple backend powered by an array in memory
 *
 * @package Cmp\Cache\Infrastureture\Backend
 */
class ChainCache extends TaggableCache
{
    use MultiCacheTrait;

    /**
     * Stored items
     * 
     * @var Cache[]
     */
    private $caches = [];

    /**
     * Pushes a cache in the chain
     *
     * @param Cache $cache
     *
     * @return $this
     */
    public function pushCache(Cache $cache)
    {
        $this->caches[] = $cache;

        return $this;
    }

    /**
     * Returns the caches in the chain
     * 
     * @return \Cmp\Cache\Cache[]
     */
    public function getCaches()
    {
        return $this->caches;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $item, $timeToLive = null)
    {
        $success = true;
        foreach ($this->caches as $cache) {
            $success = $cache->set($key, $item, $timeToLive) && $success;
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        foreach ($this->caches as $cache) {
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
        foreach ($this->caches as $index => $cache) {
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
        foreach ($this->caches as $cache) {
            $success = $cache->delete($key) && $success;
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $success = true;
        foreach ($this->caches as $cache) {
            $success = $cache->flush() && $success;
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
        foreach ($this->caches as $cache) {
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
        for (--$index; $index >= 0; $index--) {
            $this->caches[$index]->set($key, $item, $timeToLive);
        }
    }
}
