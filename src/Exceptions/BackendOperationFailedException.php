<?php

namespace Cmp\Cache\Exceptions;

use Cmp\Cache\Cache;
use Exception;

/**
 * Class BackendOperationFailedException
 *
 * @package Cmp\Cache\Exceptions
 */
class BackendOperationFailedException extends CacheException
{
    const CODE = 1004;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $operation;

    /**
     * InvalidCacheOperationException constructor.
     *
     * @param Cache          $cache
     * @param string         $operation
     * @param Exception|null $previous
     */
    public function __construct(Cache $cache, $operation, Exception $previous = null)
    {
        parent::__construct("Cache operation $operation failed for backend ".get_class($cache), $previous);
        $this->cache     = $cache;
        $this->operation = $operation;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }
}
