<?php
namespace Cmp\Cache;

interface TagCache extends Cache
{
    /**
     * @return Cache
     */
    public function tag($tagName);
}