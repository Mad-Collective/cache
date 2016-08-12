<?php
namespace Cmp\Cache\Backend;

use Cmp\Cache\TagCache;

abstract class TaggableCache implements TagCache
{
    /**
     * {@inheritdoc}
     */
    public function tag($tagName)
    {
        return new TaggedCache($this, $tagName);
    }
}
