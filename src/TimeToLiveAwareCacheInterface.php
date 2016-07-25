<?php

namespace Cmp\Cache;

/**
 * Interface TimeToLiveAwareCache
 *
 * @package Cmp\Cache
 */
interface TimeToLiveAwareCacheInterface
{
    /**
     * Gets the remaining time to live for an item
     *
     * @param $key
     *
     * @return int|null
     */
    public function getTimeToLive($key);
}