<?php

namespace Cmp\Cache\Domain\Exceptions;

use Exception;

/**
 * Class ExpiredException
 *
 * @package Cmp\Cache\Domain\Exceptions
 */
class ExpiredException extends NotFoundException
{
    const CODE = 1002;

    /**
     * ExpiredException constructor.
     *
     * @param string $key
     * @param Exception|null $previous
     */
    public function __construct($key, Exception $previous = null)
    {
        parent::__construct("The requested item '$key' has expired", $previous);
    }
}
