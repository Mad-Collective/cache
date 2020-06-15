<?php

namespace Cmp\Cache\Backend;

use Cmp\Cache\Cache;
use Cmp\Cache\Exceptions\CacheException;
use Cmp\Cache\Exceptions\ExpiredException;
use Cmp\Cache\Exceptions\NotFoundException;
use Cmp\Cache\FlushableCache;
use Cmp\Cache\PurgableCache;
use Cmp\Cache\Traits\MultiCacheTrait;

/**
 * Class ArrayCache
 * 
 * A simple backend powered by an array in memory
 *
 * @package Cmp\Cache\Infrastureture\Backend
 */
class ArrayCache extends TaggableCache
{
    use MultiCacheTrait;

    /**
     * Stored items
     * 
     * @var array
     */
    private $items = [];

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeToLive = null)
    {
        $this->items[$key] = [
            'value'      => $value,
            'expireTime' => $timeToLive === null || $timeToLive === 0 ? null : time() + $timeToLive,
        ];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if (!array_key_exists($key, $this->items)) {
            return false;
        }

        if ($this->hasExpired($key)) {
            $this->delete($key);

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        try {
            return $this->demand($key);
        } catch (CacheException $exception) {
            return $default;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function demand($key)
    {
        if (!array_key_exists($key, $this->items)) {
            throw new NotFoundException($key);
        }

        if ($this->hasExpired($key)) {
            $this->delete($key);
            throw new ExpiredException($key);
        }

        return $this->items[$key]['value'];
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function appendList($key, $value)
    {
        if (!isset($this->items[$key])) {
            $this->set($key,[$value]);
            return false;
        }

        $this->items[$key]['value'][] = $value;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key)
    {
        if (!isset($this->items[$key])) {
            $this->set($key,1);
            return false;
        }

        $this->items[$key]['value']++;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->items = [];

        return true;
    }

    /**
     * Checks whether an item as expired
     *
     * @param string $key
     *
     * @return bool
     */
    private function hasExpired($key)
    {
        return $this->items[$key]['expireTime'] !== null && $this->items[$key]['expireTime'] < time();
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
        if (!$this->has($key)) {
            return null;
        }

        return !$this->items[$key]['expireTime'] ? null : $this->items[$key]['expireTime'] - time();
    }

    /**
     * @inheritDoc
     */
    public function deleteByPrefix($prefix)
    {
        foreach($this->items as $key => $item){
            if (strpos($key, $prefix) === 0) {
                unset($this->items[$key]);
            }
        }
    }
}
