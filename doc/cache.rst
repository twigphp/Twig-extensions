Cache Extension
===============

The ``cache`` extension provides a ``cache`` tag that can be used to cache
rendered template fragments using the cache backend of your choice.

.. code-block:: jinja

    {% cache key time %}
    ...
    {% endcache %}


Here ``key`` can be both, a string or a variable name, in case it is the
later that value will be pased as the ``key`` param to your custom
backend's ``get`` and ``set`` methods. This is **very important**, if the
value of the variable with that name is an object or anything different
than a string, that value will be passed **as is**.

``time`` is an integer representing the time you want to cache this
fragment and its unit (seconds, minutes, etc) is the one choosed by your
cache-engine.

Set-up
------

In order to connect your custom cache backend to this extension all you
need to do is create a class that implements
`Twig_Extensions_Extension_Cache_CacheInterface` and then set that backend
as the one to use by the extension. By default, the extension provides two
ready to use backends:

DummyCacheBackend
''''''''''''''''''

This backend as its name suggest does nothing, which makes it convenient
when you are in a heavy development stage and not want to worry about
caching::

    <?php

    // ...

    $environment = new Twig_Environment(new Twig_Loader_Filesystem('.'));
    $dummyBackend = new Twig_Extensions_Extension_Cache_DummyCacheBackend();
    $environment->addExtension(new Twig_Extensions_Extension_Cache($dummyBackend));

    // ...


MemoizationCacheBackend
'''''''''''''''''''''''

This backend memoize_ the template fragment for the current request only::

    <?php

    // ...

    $environment = new Twig_Environment(new Twig_Loader_Filesystem('.'));
    $dummyBackend = new Twig_Extensions_Extension_Cache_MemoizationCacheBackend();
    $environment->addExtension(new Twig_Extensions_Extension_Cache($dummyBackend));

    // ...


With that in place, every time the twig environment renders a template containing
the ``{% cache ... %}`` tag, it will call your custom backend ``get($key)`` method,
if ``get($key)`` returns ``null`` then it will call your backend's ``set($key, $value, $time)``
method with the rendered chunk of template as the value of ``$value`` 


Examples
--------

Key name as string
''''''''''''''''''

Template ``test_template.txt``::

    {% cache 'cache-key' 12 %}
        {% for i in collection %}
            {{ i }}
        {% endfor %}
    {% endcache %}


Example php file::

    <?php
    $loader = new Twig_Loader_Filesystem('.');
    $environment = new Twig_Environment($loader);
    $cacheExtension = new Twig_Extensions_Extension_Cache(new Twig_Extensions_Extension_Cache_DummyCacheBackend());

    $environment->addExtension($cacheExtension);

    echo $environment->render('test_template.txt', array('collection' => range(0, 5)));

In the example above the cache key name will be 'cache-key'

Key name as a name
''''''''''''''''''

Template ``test_template.txt``::

    {% cache myobj 12 %}
        {% for i in collection %}
            {{ myobj.sayHello }}
        {% endfor %}
    {% endcache %}


Example php file::

    <?php

    class MyClass
    {
        public function sayHello()
        {
            return 'Hello';
        }

        public function __toString()
        {
            return 'myclass-instance';
        }
    }

    $myobj = new MyClass();

    $loader = new Twig_Loader_Filesystem('.');
    $environment = new Twig_Environment($loader);
    $cacheExtension = new Twig_Extensions_Extension_Cache(new Twig_Extensions_Extension_Cache_MemoizationCacheBackend());

    $environment->addExtension($cacheExtension);

    echo $environment->render('test_template.txt', array('collection' => range(0, 5), 'myobj' => $myobj));

In the example above the cache key name will be 'myclass-instance', that's
because ``Twig_Extensions_Extension_Cache_MemoizationCacheBackend``
``get`` and ``set`` methods explicitly cast the ``key`` param into a
string as follows::

    ...
    $key = (string) $key;
    ...

 And as a result, the special method ``__toString`` of the object is
called


.. _`memoize`: http://en.wikipedia.org/wiki/Memoization
