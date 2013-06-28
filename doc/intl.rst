The Intl Extension
==================

The *Intl* extensions provides the ``localizeddate`` filter.

``localizeddate``
-----------------

Use the ``localizeddate`` filter to format a ``DateTime`` object into a localized
string representating the date.

.. code-block:: jinja

    {{ post.published_at|localizeddate('medium', 'none', locale) }}

.. note::

    Internally, Twig uses the PHP `IntlDateFormatter::create()`_ function for the date.

Arguments
~~~~~~~~~

 * ``dateFormat``: The date format. Choose one of the these formats:
    * 'none': `IntlDateFormatter::NONE <http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.none>`_
    * 'short': `IntlDateFormatter::SHORT <http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.short>`_
    * 'medium': `IntlDateFormatter::MEDIUM <http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.medium>`_
    * 'long': `IntlDateFormatter::LONG <http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.long>`_
    * 'full': `IntlDateFormatter::FULL <http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.full>`_
 * ``timeFormat``: The time format. Same formats possible as above.
 * ``locale``: The locale used for the format. If ``NULL`` is given, Twig will use ``Locale::getDefault()``
 * ``timezone``: The date timezone
 * ``format``: Optional pattern to use when formatting or parsing. Possible patterns are documented at http://userguide.icu-project.org/formatparse/datetime.

.. _`IntlDateFormatter::create()`: http://php.net/manual/en/intldateformatter.create.php