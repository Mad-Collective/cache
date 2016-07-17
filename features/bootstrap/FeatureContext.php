<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Cmp\Cache\Application\CacheFactory;
use Cmp\Cache\Infrastructure\RedisCache;

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
     * FeatureContext constructor.
     */
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect($_SERVER['REDIS_HOST'], 6379);
        $this->backend = new RedisCache($this->redis); 
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
}