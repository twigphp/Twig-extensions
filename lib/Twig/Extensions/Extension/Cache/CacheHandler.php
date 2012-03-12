<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Extensions_Extension_Cache_CacheHandler
{
	protected $backend;
	protected static $instance;

	protected function __construct()
	{
		$this->backend = new Twig_Extensions_Extension_Cache_DummyCache();
	}

	public function getCacheBackend()
	{
		return $this->backend;
	}

	public function setCacheBackend(Twig_Extensions_Extension_Cache_CacheInterface $backend)
	{
		$this->backend = $backend;
	}

	public static function getInstance()
	{
		if (!(self::$instance instanceof self)) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
