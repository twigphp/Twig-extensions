<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class Twig_Extensions_Extension_Cache extends Twig_Extension
{
    /**
     * holds the cache backend in use
     *
     * @var Twig_Extensions_Extension_Cache_CacheInterface
     */
    protected $cacheBackend;

    public function __construct(Twig_Extensions_Extension_Cache_CacheInterface $cacheBackend)
    {
        $this->cacheBackend = $cacheBackend;
    }

    /**
     * Returns the cache backend in use
     *
     * @return object An object that implements Twig_Extensions_Extension_Cache_CacheInterface
     */
    public function getCacheBackend()
    {
        return $this->cacheBackend;
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(new Twig_Extensions_TokenParser_Cache());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'cache';
    }
}
