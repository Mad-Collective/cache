<?php

namespace Cmp\Cache\Factory;

use Cmp\Cache\CacheInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Redis;

/**
 * Class CacheFactoryInterface
 *
 * @package Cmp\Cache\Factory
 */
interface CacheFactoryInterface
{
    /**
     * Builds an array cache
     * 
     * @return \Cmp\Cache\Backend\ArrayCache
     */
    public function arrayCache();

    /**
     * Builds a cache with redis as a backend from the connection parameters
     * 
     * @param string $host
     * @param int    $port
     * @param int    $db
     * @param float  $timeOut
     *
     * @return \Cmp\Cache\Backend\RedisCache
     */
    public function redisFromParams($host = '127.0.0.1', $port = 6379, $db = 0, $timeOut = 0.0);

    /**
     * Builds a cache with redis as a backend
     * 
     * @param Redis $redis
     *
     * @return \Cmp\Cache\Backend\RedisCache
     */
    public function redisCache(Redis $redis);

    /**
     * Builds a chain cache
     * 
     * @param array $caches
     *
     * @return \Cmp\Cache\Backend\ChainCache
     */
    public function chainCache(array $caches = []);

    /**
     * Decorates a cache with logging
     *
     * @param CacheInterface  $cache
     * @param bool            $withExceptions
     * @param LoggerInterface $logger
     * @param string          $logLevel
     *
     * @return \Cmp\Cache\Decorator\LoggerCache
     */
    public function LoggerCache(
        CacheInterface $cache,
        $withExceptions = true,
        LoggerInterface $logger = null,
        $logLevel = LogLevel::ALERT
    );
}
