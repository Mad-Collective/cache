<?php
namespace Cmp\Cache\Backend;

use Cmp\Cache\Exceptions\ExpiredException;
use Cmp\Cache\Exceptions\NotFoundException;
use Cmp\Cache\Traits\MultiCacheTrait;

/**
 * Class NullCache
 *
 * A noop cache backend
 *
 * @package Cmp\Cache\Backend
 */
class NullCache extends TaggableCache
{
    use MultiCacheTrait;

    /**
     * {@inheritdoc}
     */
    public function set($key, $item, $timeToLive = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return false;
    }

    /**
     * Determines whether an item is in the cache or not
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return false;
    }

    /**
     * Returns an item from the cache, it throws an exception if the item is not in the cache or it has expired
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws NotFoundException When the item is not present in the cache
     * @throws ExpiredException  When the item has expired
     */
    public function demand($key)
    {
        throw new NotFoundException($key);
    }

    /**
     * {@inheritdoc}
     */
    public function appendList($key, $value)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key)
    {
        return true;
    }

    /**
     * Empties the cache
     *
     * @return bool
     */
    public function flush()
    {
        return true;
    }

    /**
     * Gets the remaining time to live for an item
     *
     * @param $key
     *
     * @return int|null
     */
    public function getTimeToLive($key)
    {
        return null;
    }

    public function deleteByPrefix($prefix)
    {
        return null;
    }
}
