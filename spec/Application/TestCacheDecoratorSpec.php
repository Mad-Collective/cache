<?php

namespace spec\Cmp\Cache\Application;

use Cmp\Cache\Domain\Cache;
use PhpSpec\ObjectBehavior;

/**
 * Class TestCacheDecoratorSpec
 *
 * @package spec\Cmp\Cache\Application
 * @mixin \Cmp\Cache\Application\TestCacheDecorator
 */
class TestCacheDecoratorSpec extends ObjectBehavior
{
    function let(Cache $decorated)
    {
        $this->beConstructedWith($decorated);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('\Cmp\Cache\Application\TestCacheDecorator');
    }

    function it_can_register_a_set_operation(Cache $decorated)
    {
        $this->hasBeenSet('foo', 'bar', 10)->shouldBe(false);
        $this->set('foo', 'bar', 10);

        $this->hasBeenSet('foo', 'bar', 10)->shouldBe(true);
        $this->hasBeenSet('foo', 'bar', 1)->shouldBe(false);
        $this->hasBeenSet('foo', 'nope')->shouldBe(false);
        $decorated->set('foo', 'bar', 10)->shouldHaveBeenCalled();
    }

    function it_can_register_a_has_operation(Cache $decorated)
    {
        $decorated->has('foo')->willReturn(true);

        $this->hasBeenChecked('foo')->shouldBe(false);
        $this->has('foo')->shouldReturn(true);
        $this->hasBeenChecked('foo')->shouldBe(true);
    }

    function it_can_register_a_get_operation_an_return_the_default(Cache $decorated)
    {
        $decorated->get('foo', 'default')->willReturn('default');

        $this->hasBeenGet('foo')->shouldBe(false);
        $this->get('foo', 'default')->shouldReturn('default');
        $this->hasBeenGet('foo')->shouldBe(true);
    }

    function it_can_register_a_demand_operation(Cache $decorated)
    {
        $decorated->demand('foo')->willReturn('bar');

        $this->hasBeenDemanded('foo')->shouldBe(false);
        $this->demand('foo')->shouldReturn('bar');
        $this->hasBeenDemanded('foo')->shouldBe(true);
    }

    function it_can_register_a_delete_operation(Cache $decorated)
    {
        $this->hasBeenDeleted('foo')->shouldBe(false);
        $this->delete('foo');

        $this->hasBeenDeleted('foo')->shouldBe(true);
        $decorated->delete('foo')->shouldHaveBeenCalled(true);
    }

    function it_can_register_a_flush_operation(Cache $decorated)
    {
        $this->hasBeenFlushed()->shouldBe(false);
        $this->flush();

        $this->hasBeenFlushed()->shouldBe(true);
        $decorated->flush()->shouldHaveBeenCalled(true);
    }

    function it_can_return_all_the_calls()
    {
        $this->flush();

        $this->getCalls()->shouldBe([
            'delete' => [],
            'set'    => [],
            'has'    => [],
            'get'    => [],
            'demand' => [],
            'flush'  => true,
        ]);
    }
}
