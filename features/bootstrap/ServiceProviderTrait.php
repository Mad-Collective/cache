<?php

namespace test\Cmp\Cache;

use Cmp\Cache\Backend\ArrayCache;
use Cmp\Cache\Backend\ChainCache;
use Cmp\Cache\Decorator\LoggerCache;
use Cmp\Cache\Factory\Pimple\CacheServiceProvider;
use Cmp\Cache\Factory\PimpleCacheProvider;
use Pimple\Container;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

/**
 * Trait ServiceProviderTrait
 */
trait ServiceProviderTrait
{
    /**
     * @var Container
     */
    private $pimple;

    /**
     * @return \Redis
     */
    abstract protected function getRedis();

    /**
     * @BeforeScenario
     */
    public function theContainerIsEmpty()
    {
        $this->pimple = new Container();
    }

    /**
     * @Given I register the service provider without options
     */
    public function iRegisterTheServiceProviderWithoutOptions()
    {
        $this->pimple->register(new CacheServiceProvider());
    }

    /**
     * @When I retrieve the cache
     */
    public function iRetrieveTheCache()
    {
        $this->pimple['cache'];
    }

    /**
     * @Then I should get an array cache
     */
    public function iShouldGetAnArrayCache()
    {
        assert($this->pimple['cache'] instanceof ArrayCache);
    }

    /**
     * @Given I register the service provider with multiple backends
     */
    public function iRegisterTheServiceProviderWithMultipleBackends()
    {
        $this->pimple['redis.connection'] = function () {
            return $this->getRedis();
        };

        $this->pimple->register(new CacheServiceProvider(), [
            'cache.backends' => [
                ['backend' => 'array'],
                ['backend' => 'redis', 'connection' => 'redis.connection'],
                ['backend' => 'redis', 'connection' => $this->getRedis()],
                ['backend' => 'redis', 'host'       => $_SERVER['REDIS_HOST'], 'port' => 6379, 'db' => 1],
                ['backend' => new ArrayCache()]
            ]
        ]);
    }

    /**
     * @Then I should get an chain cache
     */
    public function iShouldGetAnChainCache()
    {
        assert($this->pimple['cache'] instanceof ChainCache);
        assert(count($this->pimple['cache']->getCaches()) == 5);
    }

    /**
     * @Given I register the service provider with logging and silent mode
     */
    public function iRegisterTheServiceProviderWithLoggingAndSilentMode()
    {
        $this->pimple->register(new CacheServiceProvider(), [
            'cache.exceptions' => false,
            'cache.logging'    => [
                'logger' => new NullLogger(),
                'level' => LogLevel::CRITICAL
            ]
        ]);
    }

    /**
     * @Then I should get an logging decorated cache
     */
    public function iShouldGetAnLoggingDecoratedCache()
    {
        assert($this->pimple['cache'] instanceof LoggerCache);
    }
}
