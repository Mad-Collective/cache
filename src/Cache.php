<?php

namespace Cmp\Cache;

/**
 * Interface Cache
 *
 * @package Cmp\Cache
 */
interface Cache extends SimpleCache, MultiCache, TimeToLiveAwareCache
{
}
