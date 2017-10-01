The Date Extension
===================

The *Date* extension provides the ``time_diff`` filter.

You need to register this extension before using the ``time_diff`` filter::

    $twig->addExtension(new Twig_Extensions_Extension_Date());

``time_diff``
-------------

Use the ``time_diff`` filter to render the difference between a date and now.

.. code-block:: jinja

    {{ post.published_at|time_diff }}

The example above will output a string like ``4 seconds ago``  or ``in 1 month``,
depending on the filtered date.

.. code-block:: jinja

    {{ user.birth_date|time_diff("now", '%y years old') }}

The example above will output a string like ``28 years old``.	
	
.. note::

    Internally, Twig uses the PHP ``DateTime::diff()`` method for calculating the
    difference between dates, this means that PHP 5.3+ is required.

Arguments
~~~~~~~~~

* ``date``: The date for calculate the difference from now. Can be a string
  or a DateTime instance.

* ``now``: The date that should be used as now. Can be a string or
  a DateTime instance. Do not set this argument to use current date.

* ``format``: The optional format string to replace the legacy output by a 
  formatted string. The format string is same as input of PHP DateInterval.

Translation
~~~~~~~~~~~

To get a translatable output, give a ``Symfony\Component\Translation\TranslatorInterface``
as constructor argument. The returned string is formatted as ``diff.ago.XXX``
or ``diff.in.XXX`` where ``XXX`` can be any valid unit: second, minute, hour, day, month, year.
