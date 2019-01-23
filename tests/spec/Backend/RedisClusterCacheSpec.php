<?php

namespace spec\Cmp\Cache\Backend;

use Cmp\Cache\Backend\TaggedCache;
use Cmp\Cache\Exceptions\NotFoundException;
use PhpSpec\ObjectBehavior;
use RedisCluster;

/**
 * Class RedisClusterCacheSpec
 *
 * @package spec\Cmp\Cache\Infrastructure\Backend
 * @mixin \Cmp\Cache\Backend\RedisClusterCache
 */
class RedisClusterCacheSpec extends ObjectBehavior
{
    function let(RedisCluster $redis)
    {
        $this->beConstructedWith($redis);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Cache\Backend\RedisClusterCache');
        $this->shouldHaveType('Cmp\Cache\TagCache');
        $this->shouldHaveType('Cmp\Cache\Cache');
    }

    function it_can_store_items(RedisCluster $redis)
    {
        $this->set('foo', 'bar');

        $redis->set('foo', 'bar')->shouldHaveBeenCalled();
    }

    function it_can_store_multiple_items(RedisCluster $redis)
    {
        $redis->mset(['foo' => 1, 'bar' => 2])->willReturn(true);

        $this->setItems(['foo' => 1, 'bar' => 2])->shouldReturn(true);
    }

    function it_can_store_items_for_a_limited_period_of_time(RedisCluster $redis)
    {
        $this->set('foo', 'bar', 1);

        $redis->setex('foo', 1, 'bar')->shouldHaveBeenCalled();
    }

    function it_can_store_multiple_items_for_a_limited_period_of_time(RedisCluster $redis)
    {
        $redis->setex('foo', 300, 1)->willReturn(true);
        $redis->setex('bar', 300, 2)->willReturn(true);

        $this->setItems(['foo' => 1, 'bar' => 2], 300)->shouldReturn(true);
    }

    function it_delete_an_item(RedisCluster $redis)
    {
        $redis->del('foo')->willReturn(true);

        $this->delete('foo')->shouldReturn(true);
    }

    function it_delete_multiple_items_at_once(RedisCluster $redis)
    {
        $redis->del(['foo', 'bar'])->willReturn(true);

        $this->delete(['foo', 'bar'])->shouldReturn(true);
    }

    function it_can_check_the_existence_of_an_item_in_the_cache(RedisCluster $redis)
    {
        $redis->exists('foo')->willReturn(true);

        $this->has('foo')->shouldReturn(true);
    }

    function it_throws_an_exception_when_trying_to_demand_a_non_set_item(RedisCluster $redis)
    {
        $redis->get('foo')->willReturn(null);
        $redis->exists('foo')->willReturn(false);

        $this->shouldThrow(new NotFoundException('foo'))->duringDemand('foo');
    }

    function it_can_return_a_default_value_when_trying_to_get_a_non_set_item(RedisCluster $redis)
    {
        $redis->get('foo')->willReturn(null);
        $redis->exists('foo')->willReturn(false);

        $this->get('foo', 'bar')->shouldReturn('bar');
    }

    function it_gets_multiple_items_from_cache(RedisCluster $redis)
    {
        $redis->mget(['foo', 'bar'])->willReturn([0 => 1, 1 => false]);

        $this->getItems(['foo', 'bar'])->shouldReturn(['foo' => 1, 'bar' => null]);
    }

    function it_deletes_multiple_items_from_cache(RedisCluster $redis)
    {
        $redis->del('foo', 'bar')->willReturn(2);

        $this->deleteItems(['foo', 'bar'])->shouldReturn(true);
    }

    function it_can_get_the_time_to_live(RedisCluster $redis)
    {
        $redis->ttl('foo')->willReturn(false);
        $redis->ttl('bar')->willReturn(-1);
        $redis->ttl('foobar')->willReturn(15);

        $this->getTimeToLive('foo')->shouldReturn(null);
        $this->getTimeToLive('bar')->shouldReturn(null);
        $this->getTimeToLive('foobar')->shouldReturn(15);
    }

    function it_can_create_taggable()
    {
        $this->tag('dummy')->shouldHaveType(TaggedCache::class);
    }
}
