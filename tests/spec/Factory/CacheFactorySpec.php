<?php

namespace spec\Cmp\Cache\Factory;

use Cmp\Cache\CacheInterface;
use PhpSpec\ObjectBehavior;
use Redis;

/**
 * Class CacheFactorySpec
 *
 * @package spec\Cmp\Cache\Factory
 * @mixin \Cmp\Cache\Factory\CacheFactory
 */
class CacheFactorySpec extends ObjectBehavior
{
    function it_build_an_array_cache()
    {
        $this->arrayCache()->shouldBeAnInstanceOf('\Cmp\Cache\Backend\ArrayCache');
    }

    function it_can_build_a_redis_cache(Redis $redis)
    {
        $this->redisCache($redis)->shouldBeAnInstanceOf('\Cmp\Cache\Backend\RedisCache');
    }

    function it_can_chain_caches(CacheInterface $one, CacheInterface $two)
    {
        $this->chainCache([$one, $two])->shouldBeAnInstanceOf('\Cmp\Cache\Backend\ChainCache');
    }

    function it_can_decorate_a_cache_with_login(CacheInterface $cache)
    {
        $this->LoggerCache($cache)->shouldBeAnInstanceOf('\Cmp\Cache\Decorator\LoggerCache');
    }
}
