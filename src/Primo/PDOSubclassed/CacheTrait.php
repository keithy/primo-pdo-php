<?php

namespace Primo\PDOSubclassed;

trait CacheTrait
{
    public $cache = [];

    function setCache(&$cache = null)
    {
        if (!isset($cache)) $cache = [];
        $this->cache = $cache;
        return $this;
    }

    function cacheAt($k, $fnOrVal = null)
    {
        $result = null;
        if (isset($this->cache[$k])) $result = $this->cache[$k];

        else if (is_callable($fnOrVal)) $result = $this->cacheAtPut($k, $fnOrVal($k));

        else if (null !== $fnOrVal) $result = $this->cacheAtPut($k, $fnOrVal);

        return $result;
    }

    function cacheAtPut($k, $v)
    {
        $this->cache[$k] = $v;
        //$this->save();
        return $v;
    }
}
