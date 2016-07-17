<?php

namespace spec\Cmp\Cache\Application;

use Cmp\Cache\Domain\Cache;
use PhpSpec\ObjectBehavior;
use Redis;

/**
 * Class CacheFactorySpec
 *
 * @package spec\Cmp\Cache\Application
 * @mixin \Cmp\Cache\Application\CacheFactory
 */
class CacheFactorySpec extends ObjectBehavior
{
    function it_build_an_array_cache()
    {
        $this::arrayCache()->shouldBeAnInstanceOf('\Cmp\Cache\Infrastructure\ArrayCache');
    }

    function it_can_build_a_redis_cache(Redis $redis)
    {
        $this::redisCache($redis)->shouldBeAnInstanceOf('\Cmp\Cache\Infrastructure\RedisCache');
    }

    function it_can_decorate_a_cache_for_testing(Cache $cache)
    {
        $this::decorateForTesting($cache)->shouldBeAnInstanceOf('\Cmp\Cache\Application\TestCacheDecorator');
    }
}
