<?php

namespace spec\Cmp\Cache\Factory;

use Cmp\Cache\Backend\ArrayCache;
use Cmp\Cache\Backend\ChainCache;
use Cmp\Cache\Backend\RedisCache;
use Cmp\Cache\Cache;
use Cmp\Cache\Decorator\LoggerCache;
use Cmp\Cache\Factory\CacheFactoryInterface;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Redis;

/**
 * Class CacheBuilderSpec
 *
 * @package spec\Cmp\Cache\Factory
 * @mixin \Cmp\Cache\Factory\CacheBuilder
 */
class CacheBuilderSpec extends ObjectBehavior
{
    function let(CacheFactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_can_build_a_silent_cache(CacheFactoryInterface $factory, Cache $cache, LoggerCache $loggerCache)
    {
        $factory->arrayCache()->willReturn($cache);
        $factory->loggerCache($cache, false, null, LogLevel::ALERT)->willReturn($loggerCache);

        $this->withoutExceptions()->shouldReturn($this);

        $this->build()->shouldReturn($loggerCache);
    }

    function it_can_build_a_cache_with_logging(
        CacheFactoryInterface $factory,
        Cache $cache,
        LoggerInterface $logger,
        LoggerCache $loggerCache
    ) {
        $factory->arrayCache()->willReturn($cache);
        $factory->loggerCache($cache, true, $logger, LogLevel::WARNING)->willReturn($loggerCache);

        $this->withLogging($logger, LogLevel::WARNING)->shouldReturn($this);

        $this->build()->shouldReturn($loggerCache);
    }

    function it_can_chain_caches(
        CacheFactoryInterface $factory,
        ArrayCache $arrayCache,
        Redis $redis,
        Cache $anotherCache,
        RedisCache $redisOne,
        RedisCache $redisTwo,
        ChainCache $chainCache,
        LoggerCache $loggerCache
    ) {
        $factory->arrayCache()->willReturn($arrayCache);
        $factory->redisCache($redis)->willReturn($redisOne);
        $factory->redisFromParams('host', 'port', 'db', 'timeout')->willReturn($redisTwo);
        $factory->chainCache([$arrayCache, $anotherCache, $redisOne, $redisTwo])->willReturn($chainCache);
        $factory->loggerCache($chainCache, true, null, LogLevel::ALERT)->willReturn($loggerCache);

        $this->withArrayCache()->shouldReturn($this);
        $this->withCache($anotherCache)->shouldReturn($this);
        $this->withRedis($redis)->shouldReturn($this);
        $this->withRedisCacheFromParams('host', 'port', 'db', 'timeout')->shouldReturn($this);

        $this->build()->shouldReturn($loggerCache);
    }
}
