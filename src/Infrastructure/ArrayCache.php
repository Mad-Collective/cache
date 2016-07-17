<?php

namespace Cmp\Cache\Infrastructure;

use Cmp\Cache\Domain\Cache;
use Cmp\Cache\Domain\Exceptions\CacheException;
use Cmp\Cache\Domain\Exceptions\ExpiredException;
use Cmp\Cache\Domain\Exceptions\NotFoundException;

/**
 * Class ArrayCache
 * 
 * A simple backend powered by an array in memory
 *
 * @package Cmp\Cache\Infrastureture
 */
class ArrayCache implements Cache
{
    /**
     * Stored items
     * 
     * @var array
     */
    private $items = [];

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeToLive = 0)
    {
        $this->items[$key] = [
            'value'      => $value,
            'expireTime' => $timeToLive === 0 ? $timeToLive : time() + $timeToLive,
        ];
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
            return (bool) $this->delete($key);
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

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->items = [];
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
        return $this->items[$key]['expireTime'] !== 0 && $this->items[$key]['expireTime'] < time();
    }
}
