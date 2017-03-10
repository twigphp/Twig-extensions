The Locale Extension
====================

The *Locale* extensions provides access to the `Locale PHP class`_. It currently
features only the ``locale_primary_language`` function.

Installation
------------

First, :ref:`install the Extensions library<extensions-install>`. Next, add
the extension to Twig::

    $twig->addExtension(new Twig_Extensions_Extension_Locale());

``locale_primary_language``
---------------------------

Use the ``locale_primary_language`` function to get the primary language for the
current or a given locale.

.. code-block:: jinja

    <meta http-equiv="Language" content="{{ locale_primary_language() }}" />

Arguments
~~~~~~~~~

* ``locale``: The locale to obtain the primary language for. If ``NULL`` is given,
  the default (current) locale will be retrieved from ``Locale::getDefault()``.

.. _`Locale PHP class`:                      http://php.net/Locale
