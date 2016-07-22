<?php

namespace Cmp\Cache;

/**
 * Class TimeToLiveAwareCacheTrait
 *
 * @package Cmp\Cache
 */
trait TimeToLiveAwareCacheTrait
{
    /**
     * Gets the remaining time to live for an item
     *
     * @param $key
     *
     * @return int|null
     */
    public function remainingTimeToLive($key)
    {
        return null;
    }
}