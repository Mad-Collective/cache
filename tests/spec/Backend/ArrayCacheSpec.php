<?php

namespace spec\Cmp\Cache\Backend;

use Cmp\Cache\Backend\TaggedCache;
use Cmp\Cache\Exceptions\ExpiredException;
use Cmp\Cache\Exceptions\NotFoundException;
use PhpSpec\ObjectBehavior;
use Webmozart\Assert\Assert;

/**
 * Class ArrayCacheSpec
 *
 * @package spec\Cmp\Cache\Infrastructure\Backend
 * @mixin \Cmp\Cache\Backend\ArrayCache
 */
class ArrayCacheSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Cache\Backend\ArrayCache');
        $this->shouldHaveType('Cmp\Cache\TagCache');
        $this->shouldHaveType('Cmp\Cache\Cache');
    }

    function it_can_store_items()
    {
        $this->has('foo')->shouldReturn(false);

        $this->set('foo', 'bar')->shouldReturn(true);

        $this->has('foo')->shouldReturn(true);
        $this->demand('foo')->shouldReturn('bar');
    }

    function it_can_store_items_for_a_limited_period_of_time()
    {
        $this->has('foo')->shouldReturn(false);
        $this->set('foo', 'bar', 1);

        sleep(2);
        $this->shouldThrow(new ExpiredException('foo'))->duringDemand('foo');
    }

    function it_can_store_multiple_items()
    {
        $this->has('foo')->shouldReturn(false);
        $this->has('bar')->shouldReturn(false);

        $this->setItems(['foo' => 1, 'bar' => 2])->shouldReturn(true);

        $this->has('foo')->shouldReturn(true);
        $this->has('bar')->shouldReturn(true);
    }

    function it_can_get_multiple_items() 
    {
        $this->setItems(['foo' => 1, 'bar' => 2])->shouldReturn(true);

        $this->getItems(['bar', 'foo'])->shouldReturn(['bar' => 2, 'foo' => 1]);
    }

    function it_can_delete_multiple_items() 
    {
        $this->setItems(['foo' => 1, 'bar' => 2])->shouldReturn(true);

        $this->deleteItems(['foo', 'bar'])->shouldReturn(true);
        $this->has('foo')->shouldReturn(false);
        $this->has('bar')->shouldReturn(false);
    }

    function it_checking_an_expired_item_forces_a_delete()
    {
        $this->has('foo')->shouldReturn(false);
        $this->set('foo', 'bar', 1);

        sleep(2);
        $this->has('foo')->shouldBe(false);
    }

    function it_throws_an_exception_when_trying_to_demand_a_non_set_item()
    {
        $this->shouldThrow(new NotFoundException('foo'))->duringDemand('foo');
    }

    function it_can_return_a_default_value_when_trying_a_non_set_item()
    {
        $this->get('foo', 'bar')->shouldReturn('bar');
    }

    function it_can_empty_the_cache()
    {
        $this->set('foo', 'bar');
        $this->has('foo')->shouldReturn(true);

        $this->flush();
        $this->has('foo')->shouldReturn(false);
    }

    function it_can_get_the_remaining_time_to_live()
    {
        $this->set('foo', 'bar', 10);
        $this->getTimeToLive('foo')->shouldBe(10);
    }

    function it_can_return_null_for_the_time_to_live_of_infinite_items()
    {
        $this->set('foo', 'bar');
        $this->getTimeToLive('foo')->shouldBe(null);
    }

    function it_can_return_null_for_the_time_to_live_for_expired_items()
    {
        $this->set('foo', 'bar', 1);

        sleep(2);
        $this->getTimeToLive('foo')->shouldBe(null);
    }

    function it_can_create_taggable()
    {
        $this->tag('dummy')->shouldHaveType(TaggedCache::class);
    }

    function it_can_append_elements_to_a_key()
    {
        $this->appendList('foo', 'bar')->shouldBe(false);
        $this->appendList('foo', 'bar2')->shouldBe(true);
        Assert::eq($this->get('foo')->getWrappedObject(),['bar','bar2']);
    }

    function it_can_increment_value_of_a_key()
    {
        $this->increment('foo')->shouldReturn(false);
        $this->increment('foo')->shouldReturn(true);
        Assert::eq($this->get('foo')->getWrappedObject(),2);
    }

    public function it_can_delete_by_prefix()
    {
        $this->set('prefix-something1', 1);
        $this->set('prefix-something2', 1);
        $this->set('something3', 1);

        $this->deleteByPrefix('prefix');

        $this->get('prefix-something1')->shouldReturn(null);
        $this->get('prefix-something2')->shouldReturn(null);
        $this->get('something3')->shouldReturn(1);

    }
}
