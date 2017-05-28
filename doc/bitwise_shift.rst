The BitwiseShift Extension
===================

The BitwiseShift extensions provides the following operators:

* ``shl``
* ``shr``

Installation
------------

First, :ref:`install the Extensions library<extensions-install>`. Next, add
the extension to Twig::

    $twig->addExtension(new Twig_Extensions_Extension_BitwiseShift());

Usage example
-------------

This extension may be usefull when working with bitmask

.. code-block:: jinja

    {% set mask = 5 %}
    {% for packet in packets %}
        <input id="pack_{{ loop.index }}" type="checkbox" {{ ((1 shl loop.index0) b-and mask)>0 ? 'checked' }}/>
        <label for="pack_{{ loop.index }}">{{ packet }}</label>
    {% endfor %}

will check first and third checkbox (bit 1 and 3 is set)
