<?php

namespace Cmp\Cache\Infrastructure;

use Cmp\Cache\Domain\Cache;
use Cmp\Cache\Domain\ExceptionsException;
use Cmp\Cache\Domain\Exceptions\NotFoundException;
use Redis;

/**
 * Class RedisCache
 * 
 * A redis powered backend for caching
 *
 * @package Cmp\Cache\Infrastureture
 */
class RedisCache implements Cache
{
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
    public function set($key, $value, $timeToLive = 0)
    {
        if ($timeToLive > 0) {
            $this->client->setex($key, $timeToLive, $value);
        } else {
            $this->client->set($key, $value);
        }
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

        if (!$value && !$this->client->exists($key)) {
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
    public function delete($key)
    {
        $this->client->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->client->flushDB();
    }
}
