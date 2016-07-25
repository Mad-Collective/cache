<?php

namespace spec\Cmp\Cache\Exceptions;

use Cmp\Cache\CacheInterface;
use PhpSpec\ObjectBehavior;

/**
 * Class BackendOperationFailedExceptionSpec
 *
 * @package spec\Cmp\Cache\Exception
 * @mixin \Cmp\Cache\Exceptions\BackendOperationFailedException
 */
class BackendOperationFailedExceptionSpec extends ObjectBehavior
{
    function let(CacheInterface $cache)
    {
        $this->beConstructedWith($cache, 'operation');
    }

    function it_can_return_the_cache_that_failed(CacheInterface $cache)
    {
        $this->getCache()->shouldReturn($cache);
    }

    function it_can_return_the_operation_that_failed()
    {
        $this->getOperation()->shouldReturn('operation');
    }
}
