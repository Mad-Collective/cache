<?php

namespace Cmp\Cache\Decorator;

use Cmp\Cache\Cache;

/**
 * Class CacheDecorator
 *
 * @package Cmp\Cache\Decorator
 */
interface CacheDecorator extends Cache
{
    /**
     * @return Cache
     */
    public function getDecoratedCache();
}
