Twig Extensions Repository
==========================

**WARNING**: This repository is abandoned in favor of Twig Core Extra
extensions.

* *ArrayExtension*: ``shuffle`` filter -> no equivalent

* *DateExtension*: ``time_diff`` filter -> no equivalent

* *I18nExtension*: ``trans`` filter -> use the `trans
  <https://symfony.com/doc/current/reference/twig_reference.html#trans>`_ filter
  from the Symfony Translator extension (symfony/twig-bridge)

* *IntlExtension*: ``localizeddate``, ``localizednumber``, ``localizedcurrency``
  filters: use the Twig intl extra extension:
  `format_date <https://twig.symfony.com/doc/3.x/filters/format_date.html>`_,
  `format_number <https://twig.symfony.com/doc/3.x/filters/format_number.html>`_,
  `format_currency <https://twig.symfony.com/doc/3.x/filters/format_currency.html>`_,
  ...

* *TextExtension*: ``truncate``, ``wordwrap`` filters: use the Twig string extra
  extension: `u filter <https://twig.symfony.com/doc/3.x/filters/u.html>`_

This repository hosts Twig Extensions that do not belong to the core but can
be nonetheless interesting to share with other developers.

More Information
----------------

Read the `documentation`_ for more information.

.. _documentation: http://twig-extensions.readthedocs.io/
