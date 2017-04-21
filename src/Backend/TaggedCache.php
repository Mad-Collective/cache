<?php
namespace Cmp\Cache\Backend;

use Cmp\Cache\Cache;
use Cmp\Cache\Exceptions\NotFoundException;

class TaggedCache implements Cache
{
    /**
     * @var Cache
     */
    private $store;

    /**
     * @var string
     */
    private $tag;

    /**
     * @param Cache $store
     */
    public function __construct(Cache $store, $tag)
    {
        $this->store = $store;
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items, $timeToLive = null)
    {
        $keys = $this->getNamespacedKeys(array_keys($items));
        return $this->store->setItems(array_combine($keys, $items), $timeToLive);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys)
    {
        $keysMap = $this->getNamespacedKeys($keys);
        $result = $this->store->getItems(array_values($keysMap));
        $retval = [];
        foreach($keysMap as $originalKey => $transformedKey) {
            if (array_key_exists($transformedKey, $result)) {
                $retval[$originalKey] = $result[$transformedKey];
            }
        }

        return $retval;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        $keys = $this->getNamespacedKeys($keys);
        return $this->store->deleteItems(array_values($keys));
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $item, $timeToLive = null)
    {
        $key = $this->getNamespacedKey($key);
        return $this->store->set($key, $item, $timeToLive);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $key = $this->getNamespacedKey($key);
        return $this->store->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $key = $this->getNamespacedKey($key);
        return $this->store->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function demand($key)
    {
        $namespacedKey = $this->getNamespacedKey($key);
        try {
            return $this->store->demand($namespacedKey);
        } catch (NotFoundException $e) {
            throw new NotFoundException($key, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $key = $this->getNamespacedKey($key);
        return $this->store->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $pointerKey = "tag:$this->tag";
        return $this->store->delete($pointerKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeToLive($key)
    {
        $key = $this->getNamespacedKey($key);
        return $this->store->getTimeToLive($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespacedKey($key)
    {
        $result = $this->getNamespacedKeys([$key]);
        return reset($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespacedKeys(array $keys)
    {
        $retval = [];
        $prefix = $this->getTagKey();
        foreach($keys as $k) {
            $retval[$k] = "$prefix:$k";
        }
        return $retval;
    }

    /**
     * {@inheritdoc}
     */
    private function getTagKey()
    {
        $pointerKey = "tag:$this->tag";
        $key = $this->store->get($pointerKey);
        if (!is_string($key)) {
            $key = str_replace('.', '', uniqid('', true));
            $this->store->set($pointerKey, $key);
        }

        return $key;
    }
}
