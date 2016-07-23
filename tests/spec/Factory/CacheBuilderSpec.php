<?php

namespace spec\Cmp\Cache\Factory;

use Cmp\Cache\Backend\ArrayCache;
use Cmp\Cache\Backend\ChainCache;
use Cmp\Cache\Backend\RedisCache;
use Cmp\Cache\Cache;
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

    function it_can_build_a_silent_cache(CacheFactoryInterface $factory, Cache $cache)
    {
        $factory->arrayCache()->willReturn($cache);
        $factory->loggerCache($cache, false, null, LogLevel::ALERT)->willReturn($cache);

        $this->withoutExceptions()->shouldReturn($this);

        $this->build()->shouldBeAnInstanceOf($cache);
    }

    function it_can_build_a_cache_with_logging(CacheFactoryInterface $factory, Cache $cache, LoggerInterface $logger)
    {
        $factory->arrayCache()->willReturn($cache);
        $factory->loggerCache($cache, true, $logger, 'warning')->willReturn($cache);

        $this->withLogging($logger, 'warning')->shouldReturn($this);

        $this->build()->shouldBeAnInstanceOf($cache);
    }

    function it_can_chain_caches(
        CacheFactoryInterface $factory,
        ArrayCache $arrayCache,
        Redis $redis,
        Cache $anotherCache,
        RedisCache $redisOne,
        RedisCache $redisTwo,
        ChainCache $chainCache
    ) {
        $factory->arrayCache()->willReturn($arrayCache);
        $factory->redisCache($redis)->willReturn($redisOne);
        $factory->redisFromParams('host', 'port', 'db', 'timeout')->willReturn($redisTwo);
        $factory->chainCache([$arrayCache, $anotherCache, $redisOne, $redisTwo])->willReturn($chainCache);

        $this->withArrayCache()->shouldReturn($this);
        $this->withCache($anotherCache)->shouldReturn($this);
        $this->withRedis($redis)->shouldReturn($this);
        $this->withRedisCacheFromParams('host', 'port', 'db', 'timeout')->shouldReturn($this);

        $this->build()->shouldBeAnInstanceOf($chainCache);
    }
}
