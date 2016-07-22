<?php

namespace Cmp\Cache\Exceptions;

use Exception;

/**
 * Class CacheException
 *
 * @package Cmp\Cache\Exceptions
 */
class CacheException extends Exception
{
    const CODE = 1000;

    /**
     * CacheException constructor.
     *
     * @param string         $message
     * @param Exception|null $previous
     */
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, static::CODE, $previous);
    }
}
