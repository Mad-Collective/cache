<?php

namespace Cmp\Cache\Adapter;

use Cmp\Cache\Cache;
use Psr\SimpleCache\CacheInterface;

/**
 * Class Psr16Adapter
 * @package Cmp\Cache\Adapter
 */
class Psr16Adapter implements CacheInterface
{
    /**
     * @var Cache
     */
    private $cmpCache;

    /**
     * Psr16Adapter constructor.
     * @param Cache $cmpCache
     */
    public function __construct(Cache $cmpCache)
    {
        $this->cmpCache = $cmpCache;
    }

    public function get($key, $default = null)
    {
        return $this->cmpCache->get($key, $default);
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->cmpCache->set($key, $value, $ttl);
    }

    public function delete($key)
    {
        return $this->cmpCache->delete($key);
    }

    public function clear()
    {
        return $this->cmpCache->flush();
    }

    public function getMultiple($keys, $default = null)
    {
        $items = $this->cmpCache->getItems($keys);

        foreach ($keys as $key) {
            if (!array_key_exists($key, $items)) {
                $items[$key] = $default;
            }
        }

        return $items;
    }

    public function setMultiple($values, $ttl = null)
    {
        return $this->cmpCache->setItems($values, $ttl);
    }

    public function deleteMultiple($keys)
    {
        return $this->cmpCache->deleteItems($keys);
    }

    public function has($key)
    {
        return $this->cmpCache->has($key);
    }
}
