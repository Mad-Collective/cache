<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Cmp\Cache\Application\CacheFactory;
use Cmp\Cache\Application\TestCacheDecorator;
use Cmp\Cache\Infrastructure\ArrayCache;
use Cmp\Cache\Infrastructure\Provider\PimpleCacheProvider;
use Cmp\Cache\Infrastructure\RedisCache;
use Pimple\Container;

/**
 * Class FeatureContext
 */
class FeatureContext implements SnippetAcceptingContext
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var RedisCache
     */
    private $backend;

    /**
     * @var string
     */
    private $result;

    /**
     * @var Container
     */
    private $pimple;

    /**
     * FeatureContext constructor.
     */
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect($_SERVER['REDIS_HOST'], 6379);
        $this->backend = new RedisCache($this->redis);
        $this->theContainerIsEmpty(); 
    }

    /**
     * @BeforeScenario
     */
    public function reset()
    {
        $this->redis->flushDB();
    }

    /**
     * @Given The cache is empty
     */
    public function theCacheIsEmpty()
    {
        $this->redis->flushDB();
    }

    /**
     * @Given I store a an item in the cache
     */
    public function iStoreAAnItemInTheCache()
    {
        $this->backend->set('foo', 'bar');
    }

    /**
     * @When I retrieve it
     */
    public function whenIRetrieved()
    {
        $this->result = $this->backend->demand('foo');
    }

    /**
     * @Then I should get the same item
     */
    public function iShouldGetTheSameItem()
    {
        if ($this->result !== 'bar') {
            throw new \RuntimeException("The retrieve item is not the same");
        }
    }

    /**
     * @param $timeToLive
     * 
     * @Given I store a an item in the cache for :timeToLive second
     */
    public function iStoreAAnItemInTheCacheForSecond($timeToLive)
    {
        $this->backend->set('foo', 'bar', $timeToLive);
    }

    /**
     * @param $seconds
     * 
     * @When I wait :seconds seconds
     */
    public function iWaitSeconds($seconds)
    {
        sleep($seconds);
    }

    /**
     * @Then I should not be able to retrieve it
     */
    public function iShouldNotBeAbleToRetrieveIt()
    {
        if ($this->backend->has('foo')) {
            throw new \RuntimeException("Redis still has the item stored");
        }
    }

    /**
     * @When I delete the item from the cache
     */
    public function iDeleteTheItemFromTheCache()
    {
        $this->backend->delete('foo');
    }

    /**
     * @When I flush all the items item from the cache
     */
    public function iFlushAllTheItemsItemFromTheCache()
    {
        $this->backend->flush();
    }

    /**
     * @When I request a new redis cache instance to the factory
     */
    public function iRequestANewRedisCacheInstanceToTheFactory()
    {
        $redis = CacheFactory::redisFromParams($_SERVER['REDIS_HOST'], 6379, 1);
        if (!$redis instanceof RedisCache) {
            throw new RuntimeException("The factory could not create a RedisCache");
        }
    }

    /**
     * @Given I have the connection parameters for redis
     * @Then I should receive a redis cache already connected
     */
    public function doNothing()
    {

    }

    /**
     * @Given The container is empty
     */
    public function theContainerIsEmpty()
    {
        $this->pimple = new Container();
    }

    /**
     * @When I pass the options to build the array access to the provider
     */
    public function iPassTheOptionsToBuildTheArrayAccessToTheProvider()
    {
        $this->pimple->register(new PimpleCacheProvider(), ['cache.backend' => 'array']);
    }

    /**
     * @When I register the provider without any option
     */
    public function iRegisterTheProviderWithoutAnyOption()
    {
        $this->pimple->register(new PimpleCacheProvider());
    }

    /**
     * @When I pass the options to build the redis cache from parameters to the provider
     */
    public function iPassTheOptionsToBuildTheRedisCacheFromParametersToTheProvider()
    {
        $this->pimple->register(new PimpleCacheProvider(), ['cache.backend' => ['redis' => [
            'host'    => $_SERVER['REDIS_HOST'], 
            'port'    => 6379,
            'db'      => 2,
            'timeout' => 0.5,
        ]]]);
    }

    /**
     * @When I pass the options to build the redis cache from an open connection to the provider
     */
    public function iPassTheOptionsToBuildTheRedisCacheFromAnOpenConnectionToTheProvider()
    {
        $this->pimple->register(new PimpleCacheProvider(), ['cache.backend' => ['redis' => $this->redis]]);
    }

    /**
     * @When I register the provider with the debug flag set to true
     */
    public function iRegisterTheProviderWithTheDebugFlagSetToTrue()
    {
        $this->pimple->register(new PimpleCacheProvider(), ['cache.debug' => true]);
    }

    /**
     * @Then I should retrieve the test decorated cache
     */
    public function iShouldRetrieveTheTestDecoratedCache()
    {
        if (!$this->pimple['cache'] instanceof TestCacheDecorator) {
            throw new RuntimeException("The cache has not been properly decorated");
        }
    }

    /**
     * @Then I should retrieve the redis cache object
     */
    public function iShouldRetrieveTheRedisCacheObject()
    {
        if (!$this->pimple['cache'] instanceof RedisCache) {
            throw new RuntimeException("The redis cache has not been registered correctly");
        }
    }

    /**
     * @Then I should retrieve the array cache object
     */
    public function iShouldRetrieveTheArrayCacheObject()
    {
        if (!$this->pimple['cache'] instanceof ArrayCache) {
            throw new RuntimeException("The array cache has not been registered correctly");
        }
    }

    /**
     * @When I request an unknown backend cache instance to the factory
     */
    public function iRequestAnUnknownBackendCacheInstanceToTheFactory()
    {
        $this->pimple->register(new PimpleCacheProvider(), ['cache.backend' => 'foo']);
    }

    /**
     * @Then And exception should be thrown
     */
    public function andExceptionShouldBeThrown()
    {
        try {
            $this->pimple['cache'];
            throw new RuntimeException("The cache object should not be available");
        } catch (InvalidArgumentException $exception) {
            
        }
    }
}