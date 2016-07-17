<?php

namespace Cmp\Cache\Domain\Exceptions;

use Exception;

/**
 * Class NotFoundException
 *
 * @package Cmp\Cache\Domain\Exceptions
 */
class NotFoundException extends CacheException
{
    const CODE = 1001;

    /**
     * NotFoundException constructor.
     *
     * @param string         $key
     * @param Exception|null $previous
     */
    public function __construct($key, Exception $previous = null)
    {
        parent::__construct("The requested item '$key' was not found", $previous);
    }
}
