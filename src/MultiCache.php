<?php

namespace Cmp\Cache;

/**
 * Interface MultiCache
 *
 * @package Cmp\Cache
 */
interface MultiCache
{
    /**
     * Sets a list items in the cache.
     *
     * If given, time to live will determine the expiration time for the item, use 0 for infinite
     *
     * @param array $items      An associative array where the keys are the cache keys
     * @param int   $timeToLive
     *
     * @return bool
     */
    public function setItems(array $items, $timeToLive = null);

    /**
     * Gets multiple items from the cache
     *
     * @param array $keys
     *
     * @return array
     */
    public function getItems(array $keys);

    /**
     * Deletes items from the cache
     *
     * @param array $keys
     *
     * @return bool
     */
    public function deleteItems(array $keys);

    /**
     * Deletes items matching the supplied prefix
     *
     * @param string $prefix
     * @return int The number of items deleted
     */
    public function deleteByPrefix($prefix);
}
