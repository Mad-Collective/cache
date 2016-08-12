<?php

namespace Cmp\Cache\Factory;

use Cmp\Cache\Cache;
use Cmp\Cache\Decorator\LoggerCache;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Redis;

/**
 * Class CacheFactory
 *
 * @package Cmp\Cache\Application
 */
class CacheBuilder
{
    /**
     * @var CacheFactoryInterface
     */
    private $factory;

    /**
     * @var array
     */
    private $caches = [];

    /**
     * @var bool
     */
    private $withExceptions = true;

    /**
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * @var string
     */
    private $logLevel = LogLevel::ALERT;

    /**
     * CacheBuilder constructor.
     *
     * @param CacheFactoryInterface $cacheFactory
     */
    public function __construct(CacheFactoryInterface $cacheFactory = null)
    {
        $this->factory = $cacheFactory ?: new CacheFactory();
    }

    /**
     * @return $this
     */
    public function withoutExceptions()
    {
        $this->withExceptions = false;

        return $this;
    }

    /**
     * Sets a logger to log exceptions
     * 
     * @param LoggerInterface $logger
     * @param string          $logLevel
     *
     * @return $this
     */
    public function withLogging(LoggerInterface $logger, $logLevel = LogLevel::ALERT)
    {
        $this->logger   = $logger;
        $this->logLevel = $logLevel;

        return $this;
    }

    /**
     * @param string $host
     * @param int    $port
     * @param int    $db
     * @param float  $timeOut
     *
     * @return $this
     */
    public function withRedisCacheFromParams($host = '127.0.0.1', $port = 6379, $db = 0, $timeOut = 0.0)
    {
        $this->caches[] = $this->factory->redisFromParams($host, $port, $db, $timeOut);

        return $this;
    }

    /**
     * @return $this
     */
    public function withArrayCache()
    {
        $this->caches[] = $this->factory->arrayCache();

        return $this;
    }

    /**
     * @param Redis $redis
     *
     * @return $this
     */
    public function withRedis(Redis $redis)
    {
        $this->caches[] = $this->factory->redisCache($redis);

        return $this;
    }

    /**
     * @param Cache $cache
     * 
     * @return $this
     */
    public function withCache(Cache $cache)
    {
        $this->caches[] = $cache;

        return $this;
    }

    /**
     * @return Cache
     */
    private function buildCache()
    {
        $count = count($this->caches);
        if ($count == 0) {
            return $this->factory->arrayCache();
        }
        
        if ($count == 1) {
            return $this->caches[0];
        }

        return $this->factory->chainCache($this->caches);
    }

    /**
     * @return LoggerCache
     */
    public function build()
    {
        $cache = $this->buildCache();

        return $this->factory->loggerCache($cache, $this->withExceptions, $this->logger, $this->logLevel);
    }
}
