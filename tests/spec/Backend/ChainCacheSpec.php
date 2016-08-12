<?php

namespace spec\Cmp\Cache\Backend;

use Cmp\Cache\Backend\TaggedCache;
use Cmp\Cache\Cache;
use Cmp\Cache\Exceptions\NotFoundException;
use PhpSpec\ObjectBehavior;

/**
 * Class ChainCacheSpec
 *
 * @package spec\Cmp\Cache\Infrastructure\Backend
 * @mixin \Cmp\Cache\Backend\ChainCache
 */
class ChainCacheSpec extends ObjectBehavior
{
    function let(Cache $cacheOne, Cache $cacheTwo)
    {
        $this->pushCache($cacheOne)->pushCache($cacheTwo);
    } 

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Cache\Backend\ChainCache');
        $this->shouldHaveType('Cmp\Cache\TagCache');
        $this->shouldHaveType('Cmp\Cache\Cache');
    }

    function it_executes_set_command_in_all_the_caches(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->set('foo', 'bar', 123)->willReturn(true);
        $cacheTwo->set('foo', 'bar', 123)->willReturn(true);

        $this->set('foo', 'bar', 123)->shouldReturn(true);
    }

    function it_returns_false_if_a_cache_fails(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->set('foo', 'bar', 123)->willReturn(true);
        $cacheTwo->set('foo', 'bar', 123)->willReturn(false);

        $this->set('foo', 'bar', 123)->shouldReturn(false);
    }

    function it_does_not_ask_to_all_caches_if_one_has_the_item(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->has('foo')->willReturn(true);

        $this->has('foo')->shouldReturn(true);

        $cacheTwo->set('foo')->shouldNotHaveBeenCalled();
    }

    function it_tries_all_the_caches_before_failing_to_checking_for_an_item(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->has('foo')->willReturn(false);
        $cacheTwo->has('foo')->willReturn(false);

        $this->has('foo')->shouldReturn(false);
    }

    function it_does_not_ask_to_all_caches_if_one_get_the_item(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->get('foo')->willReturn('item');
        $cacheOne->getTimeToLive('foo')->willReturn(null);

        $this->get('foo')->shouldReturn('item');

        $cacheTwo->get('foo')->shouldNotHaveBeenCalled();
    }

    function it_populates_previous_caches_if_they_have_failed_to_get_the_item(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->get('foo')->willReturn(false);
        $cacheTwo->get('foo')->willReturn('item');
        $cacheTwo->getTimeToLive('foo')->willReturn(10);
        $cacheOne->set('foo', 'item', 10)->shouldBeCalled();

        $this->get('foo')->shouldReturn('item');
    }

    function it_tries_all_the_caches_before_failing_to_get_an_item(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->get('foo')->willReturn(false);
        $cacheTwo->get('foo')->willReturn(false);

        $this->get('foo', 'default')->shouldReturn('default');
    }

    function it_can_demand_an_item(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->get('foo')->willReturn(false);
        $cacheTwo->get('foo')->willReturn('bar');
        $cacheTwo->getTimeToLive('foo')->willReturn(null);
        $cacheOne->set('foo', 'bar', null)->shouldBeCalled();

        $this->demand('foo')->shouldReturn('bar');
    }

    function it_tries_all_the_caches_before_failing_to_demand_an_item(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->get('foo')->willReturn(false);
        $cacheTwo->get('foo')->willReturn(false);

        $this->shouldThrow(new NotFoundException('foo'))->duringDemand('foo');
    }

    function it_deletes_the_item_in_all_caches(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->delete('foo')->willReturn(true);
        $cacheTwo->delete('foo')->willReturn(true);

        $this->delete('foo')->shouldReturn(true);
    }

    function it_flushes_all_caches(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->flush()->willReturn(true);
        $cacheTwo->flush()->willReturn(true);

        $this->flush()->shouldReturn(true);
    }

    function it_can_retrieve_the_time_to_live(Cache $cacheOne, Cache $cacheTwo)
    {
        $cacheOne->getTimeToLive('foo')->willReturn(null);
        $cacheTwo->getTimeToLive('foo')->willReturn(100);

        $cacheOne->getTimeToLive('bar')->willReturn(null);
        $cacheTwo->getTimeToLive('bar')->willReturn(null);

        $this->getTimeToLive('foo')->shouldReturn(100);
        $this->getTimeToLive('bar')->shouldReturn(null);
    }

    function it_can_get_the_caches_in_the_chain(Cache $cacheOne, Cache $cacheTwo)
    {
        $this->getCaches()->shouldReturn([$cacheOne, $cacheTwo]);
    }

    function it_can_create_taggable()
    {
        $this->tag('dummy')->shouldHaveType(TaggedCache::class);
    }
}
