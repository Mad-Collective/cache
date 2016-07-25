<?php

namespace Cmp\Cache\Decorator;

use Cmp\Cache\CacheInterface;

/**
 * Class CacheDecorator
 *
 * @package Cmp\Cache\Decorator
 */
interface CacheDecorator extends CacheInterface
{
    /**
     * @return CacheInterface
     */
    public function getDecoratedCache();
}
