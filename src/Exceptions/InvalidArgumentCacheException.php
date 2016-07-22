<?php

namespace Cmp\Cache\Exceptions;

use Exception;
use Psr\Cache\InvalidArgumentException as PsrInvalidArgumentException;

/**
 * Class CacheException
 *
 * @package Cmp\Cache\Exceptions
 */
class InvalidArgumentException extends CacheException implements PsrInvalidArgumentException
{
    const CODE = 1003;

    /**
     * CacheException constructor.
     *
     * @param string         $argument
     * @param string         $expected
     * @param string         $value
     * @param Exception|null $previous
     */
    public function __construct($argument, $expected, $value, Exception $previous = null)
    {
        parent::__construct(
            "The given argument $argument is invalid. The expected was $expected, got ".gettype($value), 
            static::CODE, 
            $previous
        );
    }
}
