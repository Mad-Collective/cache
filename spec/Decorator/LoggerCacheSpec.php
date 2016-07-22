<?php

namespace spec\Cmp\Cache\Decorator;

use Cmp\Cache\Cache;
use Cmp\Cache\Decorator\CacheDecorator;
use Cmp\Cache\Exceptions\NotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class LoggerCacheDecoratorSpec
 *
 * @package spec\Cmp\Cache\Application
 * @mixin \Cmp\Cache\Decorator\LoggerCache
 */
class LoggerCacheSpec extends ObjectBehavior
{
    function let(CacheDecorator $decorated, LoggerInterface $logger)
    {
        $this->beConstructedWith($decorated, $withExceptions = true, $logger, LogLevel::CRITICAL);
        $decorated->getDecoratedCache()->willReturn($decorated->getWrappedObject());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('\Cmp\Cache\Decorator\LoggerCache');
        $this->shouldHaveType('\Cmp\Cache\Cache');
    }

    function it_can_return_the_decorated_cache_correctly(Cache $cache)
    {
        $this->beConstructedWith($cache);
        $this->getDecoratedCache()->shouldReturn($cache);
    }

    function it_can_get_the_inner_decorated_cache(CacheDecorator $anotherDecorator, CacheDecorator $decorated)
    {
        $this->beConstructedWith($anotherDecorator);
        $anotherDecorator->getDecoratedCache()->willReturn($decorated);

        $this->getDecoratedCache()->shouldReturn($decorated);
    }

    function it_delegates_set_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->set('one', 'foo', 10)->willReturn(true);

        $exception = new \Exception("Server's on fire!");
        $decorated->set('two', 'bar')->willThrow($exception);

        $this->set('one', 'foo', 10)->shouldBe(true);
        $this->shouldThrow('\Cmp\Cache\Exceptions\BackendOperationFailedException')->duringSet('two', 'bar');
    }

    function it_can_work_in_silent_mode(CacheDecorator $decorated)
    {
        $withExceptions = false;
        $this->beConstructedWith($decorated, $withExceptions);

        $exception = new \Exception("Server's on fire!");
        $decorated->set('foo', 'bar')->willThrow($exception);

        $this->set('foo', 'bar')->shouldReturn(false);
    }

    function it_delegates_get_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->get('foo', null)->willReturn('bar');

        $this->get('foo')->shouldReturn('bar');
    }

    function it_delegates_demand_operations_correctly(CacheDecorator $decorated, LoggerInterface $logger)
    {
        $exception = new NotFoundException('foo');
        $decorated->demand('foo')->willThrow($exception);

        $this->shouldThrow($exception)->duringDemand('foo');

        $logger->log(LogLevel::CRITICAL, Argument::containingString("cache operation failed"), [
            'cache'     => get_class($decorated),
            'decorated' => get_class($decorated->getWrappedObject()),
            'exception' => $exception,
            'method'    => Argument::containingString('demand'),
            'arguments' => ['foo'],
        ]);
    }

    function it_delegates_has_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->has('foo')->willReturn(true);

        $this->has('foo')->shouldReturn(true);
    }

    function it_delegates_delete_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->delete('foo')->willReturn(false);

        $this->delete('foo')->shouldReturn(false);
    }

    function it_delegates_delete_all_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->deleteItems(['foo', 'bar'])->willReturn(true);

        $this->deleteItems(['foo', 'bar'])->shouldReturn(true);
    }

    function it_delegates_flush_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->flush()->willReturn(false);

        $this->flush()->shouldReturn(false);
    }

    function it_delegates_set_items_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->setItems(['foo' => 'bar'], 10)->willReturn(true);

        $this->setItems(['foo' => 'bar'], 10)->shouldReturn(true);
    }

    function it_delegates_get_items_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->getItems(['foo'])->willReturn(['foo' => 'bar']);

        $this->getItems(['foo'])->shouldReturn(['foo' => 'bar']);
    }

    function it_delegates_get_time_to_live_operations_correctly(CacheDecorator $decorated)
    {
        $decorated->getTimeToLive('foo')->willReturn(false);

        $this->getTimeToLive('foo')->shouldReturn(false);
    }
}
