<?php

namespace spec\Cmp\Cache\Exceptions;

use Cmp\Cache\Cache;
use PhpSpec\ObjectBehavior;

/**
 * Class BackendOperationFailedExceptionSpec
 *
 * @package spec\Cmp\Cache\Exception
 * @mixin \Cmp\Cache\Exceptions\BackendOperationFailedException
 */
class BackendOperationFailedExceptionSpec extends ObjectBehavior
{
    function let(Cache $cache)
    {
        $this->beConstructedWith($cache, 'operation');
    }

    function it_can_return_the_cache_that_failed(Cache $cache)
    {
        $this->getCache()->shouldReturn($cache);
    }

    function it_can_return_the_operation_that_failed()
    {
        $this->getOperation()->shouldReturn('operation');
    }
}
