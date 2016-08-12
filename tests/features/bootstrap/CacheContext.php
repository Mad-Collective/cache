<?php

namespace features\Cmp\Cache;

use Behat\Behat\Context\SnippetAcceptingContext;
use Cmp\Cache\Backend\RedisCache;
use Cmp\Cache\Backend\TaggedCache;
use Cmp\Cache\Cache;
use Cmp\Cache\Decorator\LoggerCache;
use Cmp\Cache\Factory\CacheBuilder;
use RuntimeException;

/**
 * Class CacheContext
 */
class CacheContext implements SnippetAcceptingContext
{
    use ServiceProviderTrait;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var Cache
     */
    private $backend;

    /**
     * @var Cache
     */
    private $discardedBackend;

    /**
     * @var string
     */
    private $result;

    /**
     * @var string
     */
    private $lastUsedKey;

    /**
     * @var mixed
     */
    private $lastUsedValue;

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
     * @return \Redis
     */
    protected function getRedis()
    {
        return $this->redis;
    }

    /**
     * @BeforeScenario
     */
    public function reset()
    {
        $this->redis->flushDB();
    }

    /**
     * @Given I store an item in the cache
     */
    public function iStoreAnItemInTheCache()
    {
        $this->backend->set('foo', 'bar');
        $this->lastUsedKey = 'foo';
        $this->lastUsedValue = 'bar';
    }

    /**
     * @Given I store an item with key :key and value :value in the cache
     */
    public function iStoreAnItemWithKeyAndValueInTheCache($key, $value)
    {
        $this->backend->set($key, $value);
        $this->lastUsedKey = $key;
        $this->lastUsedValue = $value;
    }

    /**
     * @When I retrieve key :key
     */
    public function whenIRetrieveKey($key)
    {
        $this->result = $this->backend->get($key);
    }

    /**
     * @When I retrieve it
     */
    public function whenIRetrieved()
    {
        $this->result = $this->backend->demand($this->lastUsedKey);
    }

    /**
     * @Then I should get the same item
     */
    public function iShouldGetTheSameItem()
    {
        if ($this->result !== $this->lastUsedValue) {
            throw new RuntimeException("The retrieve item is not the same");
        }
    }

    /**
     * @Then I should not get the same item
     */
    public function iShouldNotGetTheSameItem()
    {
        if ($this->result === $this->lastUsedValue) {
            throw new RuntimeException("The retrieve item is not the same");
        }
    }

    /**
     * @Then I should get as value :value
     */
    public function iShouldGetAsValue($value)
    {
        assert($this->result == $value);
    }

    /**
     * @param $timeToLive
     *
     * @Given I store a an item in the cache for :timeToLive second
     */
    public function iStoreAAnItemInTheCacheForSecond($timeToLive)
    {
        $this->backend->set('foo', 'bar', $timeToLive);
        $this->lastUsedKey = 'foo';
        $this->lastUsedValue = 'bar';
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
        if ($this->backend->has($this->lastUsedKey)) {
            throw new RuntimeException("Redis still has the item stored");
        }
    }

    /**
     * @When I delete the item from the cache
     */
    public function iDeleteTheItemFromTheCache()
    {
        $this->backend->delete($this->lastUsedKey);
    }

    /**
     * @Given I store two items in the cache
     */
    public function iStoreTwoItemsInTheCache()
    {
        $this->backend->set('foo', 'bar');
        $this->backend->set('bar', 'foo');
    }

    /**
     * @When I delete the two items from the cache
     */
    public function iDeleteTheTwoItemsFromTheCache()
    {
        $this->backend->deleteItems(['foo', 'bar']);
    }

    /**
     * @Then I should not be able to retrieve any of them
     */
    public function iShouldNotBeAbleToRetrieveAnyOfThem()
    {
        $items = $this->backend->getItems(['foo', 'bar']);

        if ($items['foo'] !== null || $items['bar'] !== null) {
            throw new RuntimeException("There should not be any item on the cache");
        }
    }

    /**
     * @When I flush all the items from the cache
     */
    public function iFlushAllTheItemsFromTheCache()
    {
        $this->backend->flush();
    }

    /**
     * @When I request a new redis cache instance to the factory
     */
    public function iRequestANewRedisCacheInstanceToTheFactory()
    {
        $redis = (new CacheBuilder())
            ->withRedisCacheFromParams($_SERVER['REDIS_HOST'], 6379, 1)
            ->build();

        if (!$redis->getDecoratedCache() instanceof RedisCache) {
            throw new RuntimeException("The factory could not create a RedisCache");
        }
    }

    /**
     * @Given I have the connection parameters for redis
     * @Then I should receive a redis cache already connected
     */
    public function doNothing()
    {
        // no op
    }

    /**
     * @When I create a tag
     */
    public function iCreateATag()
    {
        $this->discardedBackend = $this->backend;
        $this->backend = $this->backend->tag('dummy');
    }

    /**
     * @When I discard the tag
     */
    public function iDiscardTheTag()
    {
        if (!isset($this->discardedBackend)) {
            throw new RuntimeException("No previous backend available");
        }
        $this->backend = $this->discardedBackend;
        $this->discardedBackend = null;
    }
}
