The Array Extension
===================

The Array extensions provides the following filters:

* ``shuffle``

Installation
------------

First, :ref:`install the Extensions library<extensions-install>`. Next, add
the extension to Twig::

    $twig->addExtension(new Twig_Extensions_Extension_Array());

If you're using symfony, just register extension:  

v < 3.3::

    array.twig_extension:
      class: Twig\Extensions\ArrayExtension
      public: false
      tags:
          - { name: twig.extension }
v >= 3.3::

    Twig\Extensions\ArrayExtension:
      public: false
      tags: [ 'twig.extension' ]
              
