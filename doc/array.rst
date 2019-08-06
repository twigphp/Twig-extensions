The Array Extension
===================

The Array extensions provides the following filters:

* ``shuffle``

Installation
------------

First, :ref:`install the Extensions library<extensions-install>`. Next, add
the extension to Twig::

    use Twig\Extensions\ArrayExtension;

    $twig->addExtension(new ArrayExtension());
