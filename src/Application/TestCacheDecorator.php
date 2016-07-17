<?php

namespace Cmp\Cache\Application;

/**
 * Class TestCacheDecorator
 *
 * A backend especially designed for testing
 *
 * @package Cmp\Cache\Infrastureture
 */
class TestCacheDecorator extends CacheDecorator
{
    /**
     * Calls made to the cache
     *
     * @var array
     */
    private $calls = [
        'delete' => [],
        'set'    => [],
        'has'    => [],
        'get'    => [],
        'demand' => [],
        'flush'  => false,
    ];

    /**
     * Checks if an item has been set
     *
     * @param string   $key
     * @param mixed    $value
     * @param int|null $timeToLive
     *
     * @return bool
     */
    public function hasBeenSet($key, $value = null, $timeToLive = null)
    {
        foreach ($this->calls['set'] as $set) {
            if ($this->callMatches($set, $key, $value, $timeToLive)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if an item has been checked with the 'has' method
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasBeenChecked($key)
    {
        return $this->hasBeenDoneByKey($this->calls['has'], $key);
    }

    /**
     * Checks if an item has been requested with 'get'
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasBeenGet($key)
    {
        return $this->hasBeenDoneByKey($this->calls['get'], $key);
    }

    /**
     * Checks if an item has been requested with 'demand'
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasBeenDemanded($key)
    {
        return $this->hasBeenDoneByKey($this->calls['demand'], $key);
    }

    /**
     * Checks if an item has been deleted
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasBeenDeleted($key)
    {
        return $this->hasBeenDoneByKey($this->calls['delete'], $key);
    }

    /**
     * Checks if the cache has been flushed
     *
     * @return bool
     */
    public function hasBeenFlushed()
    {
        return $this->calls['flush'];
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeToLive = 0)
    {
        $this->calls['set'][] = [
            'key'        => $key,
            'value'      => $value,
            'timeToLive' => $timeToLive,
        ];

        parent::set($key, $value, $timeToLive);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $this->calls['has'][] = $key;

        return parent::has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $this->calls['get'][] = $key;

        return parent::get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function demand($key)
    {
        $this->calls['demand'][] = $key;

        return parent::demand($key);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->calls['delete'][] = $key;

        parent::delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->calls['flush'] = true;

        parent::flush();
    }

    /**
     * Tries to match a call by the key
     *
     * @param array  $calls
     * @param string $key
     *
     * @return bool
     */
    private function hasBeenDoneByKey(array $calls, $key)
    {
        foreach ($calls as $keyOnCall) {
            if ($keyOnCall == $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array    $set
     * @param string   $key
     * @param mixed    $value
     * @param null|int $timeToLive
     *
     * @return bool
     */
    private function callMatches(array $set, $key, $value = null, $timeToLive = null)
    {
        if (
            $set['key'] != $key || 
            !$this->matches($set['value'], $value) || 
            !$this->matches($set['timeToLive'], $timeToLive)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $stored
     * @param mixed $expected
     *
     * @return bool
     */
    private function matches($stored, $expected = null)
    {
        return $expected === null || $stored == $expected;
    }
}
