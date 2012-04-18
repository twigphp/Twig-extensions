The Debug Extension
===================

.. warning::

    This extension is deprecated as of Twig 1.5. Use the Twig built-in `dump`_
    function instead.

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

.. _dump`: http://twig.sensiolabs.org/dump
