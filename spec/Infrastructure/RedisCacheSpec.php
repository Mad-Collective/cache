<?php

namespace spec\Cmp\Cache\Infrastructure;

use Cmp\Cache\Domain\Exceptions\NotFoundException;
use PhpSpec\ObjectBehavior;
use Redis;

/**
 * Class RedisCacheSpec
 *
 * @package spec\Cmp\Cache\Infrastructure
 * @mixin \Cmp\Cache\Infrastructure\RedisCache
 */
class RedisCacheSpec extends ObjectBehavior
{
    function let(Redis $redis)
    {
        $this->beConstructedWith($redis);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Cache\Infrastructure\RedisCache');
        $this->shouldHaveType('Cmp\Cache\Domain\Cache');
    }

    function it_can_store_items(Redis $redis)
    {
        $this->set('foo', 'bar');

        $redis->set('foo', 'bar')->shouldHaveBeenCalled();
    }

    function it_can_store_items_for_a_limited_period_of_time(Redis $redis)
    {
        $this->set('foo', 'bar', 1);

        $redis->setex('foo', 1, 'bar')->shouldHaveBeenCalled();
    }

    function it_can_check_the_existence_of_an_item_in_the_cache(Redis $redis)
    {
        $redis->exists('foo')->willReturn(true);

        $this->has('foo')->shouldReturn(true);
    }

    function it_throws_an_exception_when_trying_to_demand_a_non_set_item(Redis $redis)
    {
        $redis->get('foo')->willReturn(null);
        $redis->exists('foo')->willReturn(false);

        $this->shouldThrow(new NotFoundException('foo'))->duringDemand('foo');
    }

    function it_can_return_a_default_value_when_trying_to_get_a_non_set_item(Redis $redis)
    {
        $redis->get('foo')->willReturn(null);
        $redis->exists('foo')->willReturn(false);

        $this->get('foo', 'bar')->shouldReturn('bar');
    }

    function it_can_empty_the_cache(Redis $redis)
    {
        $this->flush();

        $redis->flushDB()->shouldHaveBeenCalled();
    }
}
