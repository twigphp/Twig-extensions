The Intl Extension
==================

The *Intl* extensions provides the ``localizeddate`` filter.

``localizeddate``
-----------------

Use the ``localizeddate`` filter to format dates into a localized
string representating the date.

.. code-block:: jinja

    {{ post.published_at|localizeddate('medium', 'none', locale) }}

The ``localizeddate`` filter accepts strings (it must be in a format supported by the `strtotime`_
function), `DateTime`_ instances, or `Unix timestamps`_.

.. note::

    Internally, Twig uses the PHP `IntlDateFormatter::create()`_ function for the date.

Arguments
~~~~~~~~~

 * ``dateFormat``: The date format. Choose one of these formats:
    * 'none': `IntlDateFormatter::NONE`_
    * 'short': `IntlDateFormatter::SHORT`_
    * 'medium': `IntlDateFormatter::MEDIUM`_
    * 'long': `IntlDateFormatter::LONG`_
    * 'full': `IntlDateFormatter::FULL`_
 * ``timeFormat``: The time format. Same formats possible as above.
 * ``locale``: The locale used for the format. If ``NULL`` is given, Twig will use ``Locale::getDefault()``
 * ``timezone``: The date timezone
 * ``format``: Optional pattern to use when formatting or parsing.
   Possible patterns are documented in the `ICU user guide`_.

.. _`strtotime`: http://php.net/strtotime
.. _`DateTime`: http://php.net/DateTime
.. _`Unix timestamps`: http://en.wikipedia.org/wiki/Unix_time
.. _`IntlDateFormatter::create()`: http://php.net/manual/en/intldateformatter.create.php
.. _`IntlDateFormatter::NONE`: http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.none
.. _`IntlDateFormatter::SHORT`: http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.short
.. _`IntlDateFormatter::MEDIUM`: http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.medium
.. _`IntlDateFormatter::LONG`: http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.long
.. _`IntlDateFormatter::FULL`: http://php.net/manual/en/class.intldateformatter.php#intldateformatter.constants.full
.. _`ICU user guide`: http://userguide.icu-project.org/formatparse/datetime