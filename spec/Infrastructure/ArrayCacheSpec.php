<?php

namespace spec\Cmp\Cache\Infrastructure;

use Cmp\Cache\Domain\Exceptions\ExpiredException;
use Cmp\Cache\Domain\Exceptions\NotFoundException;
use PhpSpec\ObjectBehavior;

/**
 * Class ArrayCacheSpec
 *
 * @package spec\Cmp\Cache\Infrastructure
 * @mixin \Cmp\Cache\Infrastructure\ArrayCache
 */
class ArrayCacheSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Cache\Infrastructure\ArrayCache');
        $this->shouldHaveType('Cmp\Cache\Domain\Cache');
    }
    
    function it_can_store_items()
    {
        $this->has('foo')->shouldReturn(false);
        $this->set('foo', 'bar');
        $this->has('foo')->shouldReturn(true);
    }

    function it_can_store_items_for_a_limited_period_of_time()
    {
        $this->has('foo')->shouldReturn(false);
        $this->set('foo', 'bar', 1);

        sleep(2);
        $this->shouldThrow(new ExpiredException('foo'))->duringDemand('foo');
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
}
