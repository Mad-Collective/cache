<?php

namespace spec\Cmp\Cache\Backend;

use Cmp\Cache\Backend\NullCache;
use Cmp\Cache\Exceptions\NotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class NullCacheSpec
 *
 * @package spec\Cmp\Cache\Backend
 * @mixin NullCache
 */
class NullCacheSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Cache\Backend\NullCache');
    }

    function it_sets_nothing()
    {
        $this->set('foo', 'bar', 123)->shouldReturn(false);
    }

    function it_gets_default_value_only()
    {
        $default = 'bugabuga';
        $this->get('foo', $default)->shouldReturn($default);
    }

    function it_deletes_nothing()
    {
        $this->delete('foo')->shouldReturn(false);
    }

    function it_has_nothing()
    {
        $this->has('foo')->shouldReturn(false);
    }

    function it_throws_exception_when_demanded()
    {
        $this->shouldThrow(NotFoundException::class)->duringDemand('foo');
    }

    function it_flushes_nothing_successfully()
    {
        $this->flush()->shouldReturn(true);
    }

    function it_gets_no_ttl()
    {
        $this->getTimeToLive('foo')->shouldReturn(null);
    }

    function it_can_append_elements_to_a_key()
    {
        $this->appendList('foo', 'bar')->shouldReturn(true);
    }

    function it_can_increment_value_of_a_key()
    {
        $this->increment('foo')->shouldReturn(true);
    }
}
