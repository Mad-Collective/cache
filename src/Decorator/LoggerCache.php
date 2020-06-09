<?php

namespace Cmp\Cache\Decorator;

use Cmp\Cache\Backend\TaggedCache;
use Cmp\Cache\Cache;
use Cmp\Cache\Exceptions\BackendOperationFailedException;
use Cmp\Cache\Exceptions\CacheException;
use Cmp\Cache\TagCache;
use Cmp\Cache\Traits\LoggerCacheTrait;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class LoggerCache
 *
 * @package Cmp\Cache\Decorator
 */
class LoggerCache implements TagCache, CacheDecorator
{
    use CacheDecoratorTrait, LoggerCacheTrait;

    /**
     * @var bool
     */
    private $withExceptions;

    /**
     * @var string
     */
    private $logLevel;

    /**
     * SilentCacheDecorator constructor.
     *
     * @param Cache           $cache
     * @param bool            $withExceptions
     * @param LoggerInterface $logger
     * @param string          $logLevel
     */
    public function __construct(
        Cache $cache,
        $withExceptions = true,
        LoggerInterface $logger = null,
        $logLevel = LogLevel::ALERT
    ) {
        $this->cache          = $cache;
        $this->withExceptions = $withExceptions;
        $this->logger         = $logger;
        $this->logLevel       = $logLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeToLive = null)
    {
        return $this->call(
            function() use ($key, $value, $timeToLive) {
                return $this->cache->set($key, $value, $timeToLive);
            },
            __METHOD__,
            [$key, $value, $timeToLive]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->call(
            function() use ($key) {
                return $this->cache->has($key);
            },
            __METHOD__,
            [$key]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->call(
            function() use ($key, $default) {
                return $this->cache->get($key, $default);
            },
            __METHOD__,
            [$key, $default]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function demand($key)
    {
        return $this->call(
            function() use ($key) {
                return $this->cache->demand($key);
            },
            __METHOD__,
            [$key]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->call(
            function() use ($key) {
                return $this->cache->delete($key);
            },
            __METHOD__,
            [$key]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->call(
            function() {
                return $this->cache->flush();
            },
            __METHOD__
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items, $timeToLive = null)
    {
        return $this->call(
            function() use ($items, $timeToLive) {
                return $this->cache->setItems($items, $timeToLive);
            },
            __METHOD__,
            [$items, $timeToLive]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys)
    {
        return $this->call(
            function() use ($keys) {
                return $this->cache->getItems($keys);
            },
            __METHOD__,
            [$keys]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        return $this->call(
            function() use ($keys) {
                return $this->cache->deleteItems($keys);
            },
            __METHOD__,
            [$keys]
        );
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
        return $this->call(
            function() use($key) {
                return $this->cache->getTimeToLive($key);
            },
            __METHOD__
        );
    }

    /**
     * {@inheritdoc}
     */
    public function appendList($key, $value)
    {
        return $this->call(
            function() use ($key, $value) {
                return $this->cache->set($key, $value);
            },
            __METHOD__,
            [$key, $value]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key)
    {
        return $this->call(
            function() use($key) {
                return $this->cache->increment($key);
            },
            __METHOD__
        );
    }

    /**
     * {@inheritdoc}
     */
    public function tag($tagName)
    {
        return new TaggedCache($this, $tagName);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByPrefix($prefix)
    {
        return $this->call(
            function() use($prefix) {
                return $this->cache->deleteByPrefix($prefix);
            },
            __METHOD__
        );
    }

    /**
     * @param callable $callable
     *
     * @param string   $method
     * @param array    $arguments
     *
     * @return mixed
     * @throws BackendOperationFailedException
     * @throws CacheException
     */
    protected function call(callable $callable, $method, $arguments = [])
    {
        try {
            return $callable();
        } catch (CacheException $exception) {
            $this->logException($exception, $method, $arguments);
        } catch (Exception $exception) {
            $this->logException($exception, $method, $arguments);
            $exception = new BackendOperationFailedException($this->getDecoratedCache(), $method, $exception);
        }

        if ($this->withExceptions) {
            throw $exception;
        }

        return false;
    }

    /**
     * @param Exception $exception
     * @param string    $method
     * @param array     $arguments
     *
     * @return bool
     */
    protected function logException(Exception $exception, $method, array $arguments)
    {
        $this->log($this->logLevel, "Cache $method on cache operation failed: ".$exception->getMessage(), [
            'cache'     => get_class($this->cache),
            'decorated' => get_class($this->getDecoratedCache()),
            'exception' => $exception,
            'method'    => $method,
            'arguments' => $arguments,
        ]);
    }
}
