<?php

namespace Cmp\Cache\Traits;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

/**
 * Class LoggerCacheTrait
 * 
 * Apply to a cache backend to be able to log messages without having to take care if a logger is available
 *
 * @package Cmp\Cache\Traits
 */
trait LoggerCacheTrait
{
    use LoggerAwareTrait, LoggerTrait;

    /**
     * Logs with an arbitrary level if the logger is present
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}
