<?php

namespace Cmp\Cache\Backend;

use Cmp\Cache\Cache;
use Cmp\Cache\Exceptions\NotFoundException;
use Cmp\Cache\Traits\MultiCacheTrait;
use Redis;

/**
 * Class RedisCache
 * 
 * A redis powered backend for caching
 *
 * @package Cmp\Cache\Infrastureture\Backend
 */
class RedisCache extends TaggableCache
{
    use MultiCacheTrait {
        MultiCacheTrait::setItems as setItemsTrait;
    }

    const DEFAULT_HOST    = '127.0.0.1';
    const DEFAULT_PORT    = 6379;
    const DEFAULT_DB      = 0;
    const DEFAULT_TIMEOUT = 0.0;

    /**
     * @var Redis
     */
    private $client;

    /**
     * RedisCache constructor.
     *
     * @param Redis $client
     */
    public function __construct(Redis $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeToLive = null)
    {
        if ($timeToLive > 0) {
            return $this->client->setex($key, $timeToLive, $value);
        }

        return $this->client->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items, $timeToLive = null)
    {
        if (!$timeToLive) {
            return $this->client->mset($items);
        }

        return $this->setItemsTrait($items, $timeToLive);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->client->exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function demand($key)
    {
        $value = $this->client->get($key);

        if (!$value) {
            throw new NotFoundException($key);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        try {
            return $this->demand($key);
        } catch (NotFoundException $exception) {
            return $default;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys)
    {
        $items = [];

        $values = $this->client->mget($keys);
        foreach ($keys as $index => $key) {
            $items[$key] = $values[$index] === false ? null : $values[$index];
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return (bool) $this->client->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        return call_user_func_array([$this->client, 'delete'], $keys) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function appendList($key, $value)
    {
        return (bool) $this->client->append($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key)
    {
        return (bool) $this->client->incr($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->client->flushDB();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeToLive($key)
    {
        $timeToLive = $this->client->ttl($key);

        return false === $timeToLive || $timeToLive <= 0 ? null : $timeToLive; 
    }
}
