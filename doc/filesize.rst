The Filesize Extension
======================

The *Filesize* extension provides the ``filesize`` filter.

You need to register this extension before using the ``filesize`` filter::

    $twig->addExtension(new Twig_Extensions_Extension_Filesize());

``filesize``
------------

Use the ``filesize`` filter to render a human readable filesize

.. code-block:: jinja

    {{ download.size|filesize }}

The example above will output a filesize in byte like ``4 MiB``  or ``24 KiB``,
depending on the filtered filesize.

Arguments
~~~~~~~~~

* ``size``: A float/int/string of the size to format

* ``fixed_suffix``: Optionally use this fixed suffix to calculate. Must be a
    correct filesize suffix and corresponding to ``power_of_two``.

* ``power_of_two``: True or false, whether to use powers of 2 or 10.

* ``decimal``: The number of decimal points to display

* ``decimal_point``: The character(s) to use for the decimal point

* ``thousand_sep``: The character(s) to use for the thousands separator

Defaults
~~~~~~~~

If no formatting options are provided then Twig will use the default formatting
options of:

* Suffix is calculated automatically.
* Power of 2 for the calculation base.
* 0 decimal places.
* ``.`` as the decimal point.
* ``,`` as the thousands separator.

The filesize defaults (suffix and power of 2) can either be set in the
constructor of the extension:

.. code-block:: php

    $twig->addExtension(new Twig_Extensions_Extension_Filesize('MB', false));

Or through retrieving the filesize extension and a setter:

.. code-block:: php

    $twig->getExtension('filesize')->setDefaults('MB', false);

The number formatting defaults are retrieved through the defaults of the core
extension and can be easily changed through the core extension:

.. code-block:: php

    $twig->getExtension('core')->setNumberFormat(3, '.', ',');

The defaults set for ``filesize`` can be over-ridden upon each call using the
additional parameters.

