<?php

namespace Cmp\Cache\Decorator;

use Cmp\Cache\CacheInterface;

/**
 * Class CacheDecoratorTrait
 *
 * @package Cmp\Cache\Decorator
 */
trait CacheDecoratorTrait
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @return CacheInterface
     */
    public function getDecoratedCache()
    {
        if ($this->cache instanceof CacheDecorator) {
            return $this->cache->getDecoratedCache();
        }

        return $this->cache;
    }
}
