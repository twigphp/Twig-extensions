The Debug Extension
===================

.. caution::

    The debug extension (``Twig_Extensions_Extension_Debug``) coming from the
    ``Twig-extensions`` package is deprecated as of Twig 1.5. Use the Twig
    built-in `dump`_ function from the ``Twig_Extension_Debug`` extension
    instead.

The ``debug`` extension provides a ``debug`` tag that can be used to
output the content of the current context:

.. code-block:: jinja

    {% debug %}

This is really useful when a template does not work as expected. You can also
output a specific variable or an expression:

.. code-block:: jinja

    {% debug items %}

    {% debug post.body %}

.. caution::

    The ``debug`` tag only works when the ``debug`` environment option is set
    to ``true``.

.. _`dump`: http://twig.sensiolabs.org/dump
