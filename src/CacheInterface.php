<?php

namespace Cmp\Cache;

/**
 * Interface Cache
 *
 * @package Cmp\Cache
 */
interface CacheInterface extends SimpleCacheInterface, MultiCacheInterface, TimeToLiveAwareCacheInterface
{
}
