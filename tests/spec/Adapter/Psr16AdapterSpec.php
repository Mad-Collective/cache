<?php

namespace spec\Cmp\Cache\Adapter;

use Cmp\Cache\Cache;
use PhpSpec\ObjectBehavior;
use Psr\SimpleCache\CacheInterface;

/**
 * Class Psr16AdapterSpec
 * @package spec\Cmp\Cache\Adapter
 * @mixin \Cmp\Cache\Adapter\Psr16Adapter
 */
class Psr16AdapterSpec extends ObjectBehavior
{
    function let(Cache $cmpCache)
    {
        $this->beConstructedWith($cmpCache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CacheInterface::class);
    }

    function it_delegates_get_operations_correctly(Cache $cmpCache)
    {
        $cmpCache->get('one', 'two')->willReturn('data');

        $this->get('one', 'two')->shouldReturn('data');
    }

    function it_delegates_set_operations_correctly(Cache $cmpCache)
    {
        $cmpCache->set('one', 'two', 10)->willReturn(true);

        $this->set('one', 'two', 10)->shouldBe(true);
    }

    function it_delegates_delete_operations_correctly(Cache $cmpCache)
    {
        $cmpCache->delete('foo')->willReturn(false);

        $this->delete('foo')->shouldReturn(false);
    }

    function it_delegates_clear_operations_correctly(Cache $cmpCache)
    {
        $cmpCache->flush()->willReturn(false);

        $this->clear()->shouldReturn(false);
    }

    function it_delegates_get_multiple_operations_correctly_applying_default(Cache $cmpCache)
    {
        $cmpCache->getItems(['foo', 'nope'])->willReturn(['foo' => 'bar']);

        $this->getMultiple(['foo', 'nope'], 'default')->shouldReturn(['foo' => 'bar', 'nope' => 'default']);
    }

    function it_delegates_set_multiple_operations_correctly(Cache $cmpCache)
    {
        $cmpCache->setItems(['foo' => 'bar'], 10)->willReturn(true);

        $this->setMultiple(['foo' => 'bar'], 10)->shouldReturn(true);
    }

    function it_delegates_delete_multiple_operations_correctly(Cache $cmpCache)
    {
        $cmpCache->deleteItems(['foo', 'bar'])->willReturn(true);

        $this->deleteMultiple(['foo', 'bar'])->shouldReturn(true);
    }

    function it_delegates_has_operations_correctly(Cache $cmpCache)
    {
        $cmpCache->has('foo')->willReturn(true);

        $this->has('foo')->shouldReturn(true);
    }
}
