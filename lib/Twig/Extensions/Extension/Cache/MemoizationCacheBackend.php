<?php
/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class Twig_Extensions_Extension_Cache_MemoizationCacheBackend implements Twig_Extensions_Extension_Cache_CacheInterface
{
    protected $slots;

    public function __construct()
    {
        $this->slots = array();
    }

    public function get($key)
    {
        $key = (string) $key;
        if (isset($this->slots[$key])) {
            return $this->slots[$key];
        }

        return null;
    }

    public function set($key, $value, $time = null)
    {
        $key = (string) $key;
        $this->slots[$key] = $value;
    }
}
