The Date Extension
===================

The *Date* extension provides the ``time_diff`` filter.

You need to register this extension before using the ``time_diff`` filter::

    $twig->addExtension(new Twig_Extensions_Extension_Date());
    
If you're using symfony, just register extension:  

v < 3.3::

    date.twig_extension:
      class: Twig\Extensions\DateExtension
      public: false
      tags:
          - { name: twig.extension }
v >= 3.3::

    Twig\Extensions\DateExtension:
      public: false
      tags: [ 'twig.extension' ]

``time_diff``
-------------

Use the ``time_diff`` filter to render the difference between a date and now.

.. code-block:: jinja

    {{ post.published_at|time_diff }}

The example above will output a string like ``4 seconds ago``  or ``in 1 month``,
depending on the filtered date.

.. note::

    Internally, Twig uses the PHP ``DateTime::diff()`` method for calculating the
    difference between dates, this means that PHP 5.3+ is required.

Arguments
~~~~~~~~~

* ``date``: The date for calculate the difference from now. Can be a string
  or a DateTime instance.

* ``now``: The date that should be used as now. Can be a string or
  a DateTime instance. Do not set this argument to use current date.

Translation
~~~~~~~~~~~

To get a translatable output, give a ``Symfony\Component\Translation\TranslatorInterface``
as constructor argument. The returned string is formatted as ``diff.ago.XXX``
or ``diff.in.XXX`` where ``XXX`` can be any valid unit: second, minute, hour, day, month, year.
