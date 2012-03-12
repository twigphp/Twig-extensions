Twig Extensions Repository
==========================

This repository hosts Twig Extensions that do not belong to the core but can
be nonetheless interesting to share with other developers.

Fork this repository, add your extension, and request a pull.


Cache Extension
---------------

This extensions provides a custom tag `{% cache 'key' time %}` which can be used
to cache "rendered" template fragments using the cache backend of your choice.
In order to use your custom cache backend all you need to do is create a class
that implements `Twig_Extensions_Extension_Cache_CacheInterface` and then set
that backend as the one to use by the cache handler class, here's an example:

```php
<?php

class CacheBackendBridge extends MyCustomCacheBackend implements Twig_Extensions_Extension_Cache_CacheInterface
{
	public function get($key)
	{
		// ...
	}

	public function set($key, $value, $time)
	{
		// ...
	}
}

Twig_Extensions_Extension_Cache_CacheHandler::getInstance()->setCacheBackend(new CacheBackendBridge());
```

With that in place, every time the twig environment renders a template containing the `{% cache ... %}` tag, it will call
your custom backend `get($key)` method, if `get($key)` returns `null` then it will call your backend's `set($key, $value, $time)`
method with the rendered chunk of template as the value of `$value` 

By default the extension uses an instance of `Twig_Extensions_Extension_Cache_DummyCache` class that, like its name says, caches
nothing and another ready to use class named `Twig_Extensions_Extension_Cache_MemoizationCache` that all it does is
[memoize](http://en.wikipedia.org/wiki/Memoization "See Memoization") the template fragment for the current request only.
