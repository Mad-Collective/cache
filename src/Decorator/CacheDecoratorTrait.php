<?php

namespace Cmp\Cache\Decorator;

use Cmp\Cache\Cache;

/**
 * Class CacheDecoratorTrait
 *
 * @package Cmp\Cache\Decorator
 */
trait CacheDecoratorTrait
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @return Cache
     */
    public function getDecoratedCache()
    {
        if ($this->cache instanceof CacheDecorator) {
            return $this->cache->getDecoratedCache();
        }

        return $this->cache;
    }
}
