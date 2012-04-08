<?php
/*
 * This file is part of Twig.
 *
 * (c) 2012 Mickael Desfrenes <desfrenes@gmail.com> 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extensions_Extension_Cache_APCBackend implements Twig_Extensions_Extension_Cache_CacheInterface
{
    public function get($key)
    {
        return apc_fetch($key);
    }

    public function set($key, $value, $time = 0)
    {
        return apc_store($key, $value, $time);
    }
}
