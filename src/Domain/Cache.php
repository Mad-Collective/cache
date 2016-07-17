<?php

namespace Cmp\Cache\Domain;

use Cmp\Cache\Domain\Exceptions\ExpiredException;
use Cmp\Cache\Domain\Exceptions\NotFoundException;

/**
 * Interface Cache
 *
 * @package Cmp\Cache\Domain
 */
interface Cache
{
    /**
     * Sets an item in the cache.
     *
     * If given, time to live will determine the expiration time for the item, use 0 for infinite
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $timeToLive
     */
    public function set($key, $value, $timeToLive = 0);

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
     */
    public function delete($key);

    /**
     * Empties the cache
     */
    public function flush();
}
