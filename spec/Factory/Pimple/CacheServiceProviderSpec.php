<?php

namespace spec\Cmp\Cache\Factory\Pimple;

use Cmp\Cache\Backend\RedisCache;
use Cmp\Cache\Cache;
use Cmp\Cache\Factory\CacheBuilder;
use PhpSpec\ObjectBehavior;
use Pimple\Container;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class CacheServiceProviderSpec
 *
 * @package spec\Cmp\Cache\Factory
 * @mixin \Cmp\Cache\Factory\Pimple\CacheServiceProvider
 */
class CacheServiceProviderSpec extends ObjectBehavior
{
    function let(CacheBuilder $builder)
    {
        $this->beConstructedWith($builder);
    }

    function it_is_initializable()
    {
        $this->beAnInstanceOf('\Cmp\Cache\Factory\Pimple\CacheServiceProvider');
        $this->beAnInstanceOf('\Pimple\ServiceProviderInterface');
    }

    function it_can_register_default_cache_services(Container $container)
    {
        $this->register($container);

        $container->offsetSet('cache', Argument::type('callable'))->shouldHaveBeenCalled();
        $container->offsetSet('cache.backends', ['array' => ['backend' => 'array']])->shouldHaveBeenCalled(); 
        $container->offsetSet('cache.exceptions', true)->shouldHaveBeenCalled(); 
        $container->offsetSet('cache.logging', ['logger' => null, 'level' => LogLevel::ALERT])->shouldHaveBeenCalled();
    }

    function it_can_build_the_cache_with_defaults(
        CacheBuilder $builder,
        LoggerInterface $logger,
        \Redis $redisOne,
        \Redis $redisTwo,
        Cache $backend
    ) {
        $container = new Container();
        $container['redis.connection'] = function() use($redisOne) {
            return $redisOne->getWrappedObject(); 
        };

        $container->register($this->getWrappedObject(), [
            'cache.backends' => [
                ['backend' => 'array'],
                ['backend' => 'redis', 'connection' => 'redis.connection'], 
                ['backend' => 'redis', 'connection' => $redisTwo->getWrappedObject()],
                ['backend' => 'redis', 'host'       => '8.8.8.8', 'port' => 1234, 'db' => 1, 'timeout' => 1.5],
                ['backend' => $backend->getWrappedObject()]
            ],
            'cache.exceptions' => false,
            'cache.logging'    => ['logger' => $logger->getWrappedObject(), 'level' => LogLevel::CRITICAL]
        ]);

        $builder->withArrayCache()->shouldBeCalled();
        $builder->withRedis($redisOne)->shouldBeCalled();
        $builder->withRedis($redisTwo)->shouldBeCalled();
        $builder->withRedisCacheFromParams('8.8.8.8', 1234, 1, 1.5)->shouldBeCalled();
        $builder->withCache($backend)->shouldBeCalled();
        $builder->withoutExceptions()->shouldBeCalled();
        $builder->withLogging($logger, LogLevel::CRITICAL)->shouldBeCalled();
        $builder->build()->willReturn('foo');

        assert($container['cache'] == 'foo');
    }
}
