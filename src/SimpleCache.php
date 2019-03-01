<?php

namespace Cmp\Cache;

use Cmp\Cache\Exceptions\ExpiredException;
use Cmp\Cache\Exceptions\NotFoundException;

/**
 * Interface Cache
 *
 * @package Cmp\Cache
 */
interface SimpleCache
{
    /**
     * Sets an item in the cache.
     *
     * If given, time to live will determine the expiration time for the item, use 0 for infinite
     *
     * @param string $key
     * @param mixed  $item
     * @param int    $timeToLive Null or zero for infinite
     *
     * @return bool
     */
    public function set($key, $item, $timeToLive = null);

    /**
     * Determines whether an item is in the cache or not
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Tries to get an item, and if it's not present, it returns the given default
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

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
    public function demand($key);

    /**
     * Deletes an item form the cache
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key);

    /**
     * Empties the cache
     *
     * @return bool
     */
    public function flush();

    /**
     * Appends the value at the end of the list
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     */
    public function appendList($key, $value);

    /**
     * Increments the number stored at key by one
     *
     * @param string $key
     *
     * @return bool
     */
    public function increment($key);
}
