<?php
/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class Twig_Extensions_Extension_Cache_DummyCacheBackend implements Twig_Extensions_Extension_Cache_CacheInterface
{
    public function get($key)
    {
        return null;
    }

    public function set($key, $value, $time = null)
    {
        // does nothing...
    }
}
