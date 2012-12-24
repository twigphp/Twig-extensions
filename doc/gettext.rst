The Gettext Extension
=====================

The Gettext extension adds complete `gettext`_ support to Twig. It defines a whole host of functions that can be used in Twig templates. It also adds an ``Extractor`` class, which can parse Twig templates and extract all strings marked for localization. The gettext support includes:

* categories
* domains
* context
* plurals
* extractable comments
* convenience wrapper for string formatting

Configuration
-------------

You need to register this extension before using any of the gettext functions::

    $twig->addExtension(new Twig_Extensions_Extension_Gettext);

Note that you must configure the PHP ``gettext`` extension before rendering any
internationalized template. Here is a simple configuration example from the
PHP `documentation`_::

    // Set language to French
    putenv('LC_ALL=fr_FR');
    setlocale(LC_ALL, 'fr_FR');

    // Specify the location of the translation tables
    bindtextdomain('myAppPhp', 'includes/locale');
    bind_textdomain_codeset('myAppPhp', 'UTF-8');

    // Choose domain
    textdomain('myAppPhp');

.. caution::

    The Gettext extension only works if the PHP `gettext`_ extension is
    enabled.
    
The ``Twig_Extensions_Extension_Gettext`` class accepts one constructor argument: ``$useShortnames``. By default it is set to ``true``, which means every gettext function is aliased to a shortname. If this conflicts with another extension, set it to false::

    $twig->addExtension(new Twig_Extensions_Extension_Gettext(false));

The examples below assume shortnames are on, see :ref:`API documentation <api-docs>` for alternative names.

    
Usage
-----

Wrap any translatable strings in your templates into one of the appropriate gettext functions:

.. code-block:: jinja

    <h1>{{ _('Hello World!') }}</h1>
    <p>{{ _nf('It has been one day without apocalypse.', 'It has been %d days without apocalypse.', n, n) }}</p>
    
    {#
       The %s is an noun, the %d a number. If you need to
       switch the order of the placeholders for translation,
       use %1$s and %2$d instead.
    #}
    <p>{{ _f('The %s contains %d monkeys', thing, num) }}</p>
    
    <input type="submit" value="{{ _p('verb', 'Update') }}">
    
    {% if someError %}
        <p>{{ _d('errors', 'Some error occurred!') }}</p>
    {% endif %}
    
All gettext functions either start with an underscore (short aliases) or end in ``gettext`` (full name). There are many variations of the basic ``_()``/``gettext()`` function, each with an array of letters tagged on. The letters are shorthand for various attributes that can be modified about the translatable string:

* ``n``: pluralization
* ``d``: override default domain
* ``c``: override ``LC_MESSAGES`` category
* ``p``: put the string in a context
* ``f``: convenience wrapper for `sprintf`_

The full `GNU gettext documentation`_ details the usage of these different aspects and I highly recommend you read it. A short summary follows.

The translation files are organized in a directory structure like this:

.. code-block:: text

    locale/
        en_US/
            LC_MESSAGES/
                default.mo
                errors.mo
                ...
            LC_MONETARY/
                default.mo
                ...
            ...
        ...

``en_US`` is the *locale*, which is selected using the ``setlocale`` function. ``LC_MESSAGES``, ``LC_MONETARY`` are *categories*, each category can be switched to use a different locale; for instance you can localize text to English while formatting numbers and times in French format, if your users so desire. The names of the ``.mo`` files are the *domain*, they help you organize your strings into groups. Inside the ``.mo`` files a string may be marked with a *context*. Contexts help you distiguish between two identical strings which may translate differently, for example ``_p('verb', 'Update')`` and ``_p('noun', 'Update')``. Try to use these distictions while writing code, it makes the translation job easier later on.

The ``f`` functions are a convenience wrapper added by the Twig ``gettext`` extension. They allow you to pass an arbitrary number of parameters which will be used as parameters to `sprintf`_ after localizing the string. For example:

.. code-block:: php

    // regular PHP
    <?php printf(_('The %s contains %d monkeys'), $thing, $num); ?>

.. code-block:: jinja

    {# Twig gettext equivalent #}
    {{ _f('The %s contains %d monkeys', thing, num) }}

.. code-block:: php

    // regular PHP
    <?php printf(ngettext('The %s contains one monkey', 'The %s contains %d monkeys', $num), $thing, $num); ?>
    
.. code-block:: jinja
    
    {# Twig gettext equivalent #}
    {{ _nf('The %s contains one monkey', 'The %s contains %d monkeys', num, thing, num) }}


String extraction
-----------------

Automated string extraction is an important step in working with ``gettext``. You should never manually edit ``.po`` files or add entries to them, this needs to happen automatically from the prepared source code or you'll have a really hard time coordinating updated source strings with translated files. The Twig ``gettext`` extension comes with a class that parses the Twig template files and returns an array of extracted strings: ``Twig_Extensions_Extension_Gettext_Extractor``.

To generate ``.pot`` files from the returned array, you need a tool that can merge all extracted strings into a catalog and write this catalog into the various ``.pot`` files. The Twig ``gettext`` extension comes with an adapter to the `Kunststube\\POTools`_ library which handles this job. Assuming you have installed this library and it is autoloading, an extraction script can look like this::

    Twig_Autoloader::register();
    Twig_Extensions_Autoloader::register();

    $poFactory = new Twig_Extensions_Extension_Gettext_POString_Kunststube_Adapter_Factory;
    $extractor = new Twig_Extensions_Extension_Gettext_Extractor($poFactory);
    $catalog   = new Kunststube\POTools\Catalog;

    $templatesDir = 'templates';
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($templatesDir), RecursiveIteratorIterator::LEAVES_ONLY) as $file)

        if ($file->isFile()) {
            $strings = $extractor->extractFile($file);
            foreach ($strings as $string) {
                $catalog->add($string);
            }
        }

    }
    
    $catalog->writeToDirectory('locale/en');


You can write your own tools if you have different needs. All you need is a class that implements ``Twig_Extensions_Extension_Gettext_POString_Interface``. This is simply a container object that represents one translatable string with all its different possible attributes like domain, context etc. You then pass a factory that implements ``Twig_Extensions_Extension_Gettext_POString_Factory_Interface`` to the extractor class, which allows the extractor to generate one such container object for each extracted string and return an array of such objects. The catalog in the above example has the job of merging and grouping these and writing them into files with the correct format.


Comments
^^^^^^^^

The ``Twig_Extensions_Extension_Gettext_Extractor`` extracts Twig comments on the line(s) immediately preceeding the line with the ``gettext`` function. This allows the programmer to annotate translatable strings with instructions for the translator. It is an important tool for making the translation process smoother and producing high quality translations. For example:

.. code-block:: jinja

    {# Please do not translate "Foo", it is our product name and the whole sentence is a word play. #}
    <p>{{ _("Get Foobar'd today!") }}</p>
    
The extracted ``.po`` file will contain:

.. code-block:: text

    #. Please do not translate "Foo", it is our product name and the whole sentence is a word play.
    msgid "Get Foobar'd today!"
    msgstr ""

If there is one or more lines of whitespace between the comment and the ``gettext`` function, the comment won't be extracted.

.. caution::

    *Any* comment block on the preceeding line will be extracted. Take care that it's not a commented-out block of code.


.. _api-docs:

API
---

* ``gettext``, ``_``

  Basic translation in default domain and ``LC_MESSAGES`` category.

  .. code-block:: jinja
  
      {{ gettext('String') }}
      {{ _('String') }}

* ``fgettext``, ``_f``

  Translation in default domain and ``LC_MESSAGES`` category with string formatting.

  .. code-block:: jinja
  
      {{ fgettext('String', arg, ..) }}
      {{ _f('String', arg, ..) }}

* ``pgettext``, ``_p``

  Translation in default domain and ``LC_MESSAGES`` category with context.

  .. code-block:: jinja
  
      {{ pgettext('context', 'String') }}
      {{ _p('context', 'String') }}

* ``pfgettext``, ``_pf``

  Translation in default domain and ``LC_MESSAGES`` category with context and string formatting.

  .. code-block:: jinja
  
      {{ pfgettext('context', 'String', arg, ..) }}
      {{ _pf('context', 'String', arg, ..) }}

* ``ngettext``, ``_n``

  Pluralized translation in default domain and ``LC_MESSAGES`` category.

  .. code-block:: jinja
  
      {{ ngettext('Singular', 'Plural', num) }}
      {{ _n('Singular', 'Plural', num) }}

* ``nfgettext``, ``_nf``

  Pluralized translation in default domain and ``LC_MESSAGES`` category with string formatting.

  .. code-block:: jinja
  
      {{ nfgettext('Singular', 'Plural', num, arg, ..) }}
      {{ _nf('Singular', 'Plural', num, arg, ..) }}

* ``npgettext``, ``_np``

  Pluralized translation in default domain and ``LC_MESSAGES`` category with context.

  .. code-block:: jinja
  
      {{ npgettext('context', 'Singular', 'Plural', num) }}
      {{ _np('context', 'Singular', 'Plural', num) }}

* ``npfgettext``, ``_npf``

  Pluralized translation in default domain and ``LC_MESSAGES`` category with context and string formatting.

  .. code-block:: jinja
  
      {{ npfgettext('context', 'Singular', 'Plural', num, arg, ..) }}
      {{ _npf('context', 'Singular', 'Plural', num, arg, ..) }}

* ``dgettext``, ``_d``

  Translation in ``LC_MESSAGES`` category and specified domain.

  .. code-block:: jinja
  
      {{ dgettext('domain', 'String') }}
      {{ _d('domain', 'String') }}

* ``dfgettext``, ``_df``

  Translation in ``LC_MESSAGES`` category and specified domain with string formatting.

  .. code-block:: jinja
  
      {{ dfgettext('domain', 'String', arg, ..) }}
      {{ _df('domain', 'String', arg, ..) }}

* ``dpgettext``, ``_dp``

  Translation in ``LC_MESSAGES`` category and specified domain with context.

  .. code-block:: jinja
  
      {{ dpgettext('context', 'domain', 'String') }}
      {{ _dp('context', 'domain', 'String') }}

* ``dpfgettext``, ``_dpf``

  Translation in ``LC_MESSAGES`` category and specified domain with context and string formatting.

  .. code-block:: jinja
  
      {{ dpfgettext('context', 'domain', 'String', arg, ..) }}
      {{ _dpf('context', 'domain', 'String', arg, ..) }}

* ``dngettext``, ``_dn``

  Pluralized translation in ``LC_MESSAGES`` category and specified domain.

  .. code-block:: jinja
  
      {{ dngettext('domain', 'Singular', 'Plural', num) }}
      {{ _dn('domain', 'Singular', 'Plural', num) }}


* ``dnfgettext``, ``_dnf``

  Pluralized translation in ``LC_MESSAGES`` category and specified domain with string formatting.

  .. code-block:: jinja
  
      {{ dnfgettext('domain', 'Singular', 'Plural', num, arg, ..) }}
      {{ _dnf('domain', 'Singular', 'Plural', num, arg, ..) }}

* ``dnpgettext``, ``_dnp``

  Pluralized translation in ``LC_MESSAGES`` category and specified domain with context.

  .. code-block:: jinja
  
      {{ dnpgettext('context, 'domain', 'Singular', 'Plural', num) }}
      {{ _dnp('context', 'domain', 'Singular', 'Plural', num) }}

* ``dnpfgettext``, ``_dnpf``

  Pluralized translation in ``LC_MESSAGES`` category and specified domain with context and string formatting.

  .. code-block:: jinja
  
      {{ dnpfgettext('context, 'domain', 'Singular', 'Plural', num, arg, ..) }}
      {{ _dnpf('context', 'domain', 'Singular', 'Plural', num, arg, ..) }}

* ``dcgettext``, ``_dc``

  Translation in specified domain and category.

  .. code-block:: jinja
  
      {{ dcgettext('domain', 'String', 'category') }}
      {{ _dc('domain', 'String', 'category') }}

* ``dcfgettext``, ``_dcf``

  Translation in specified domain and category with string formatting.

  .. code-block:: jinja
  
      {{ dcfgettext('domain', 'String', 'category', arg, ..) }}
      {{ _dcf('domain', 'String', 'category', arg, ..) }}

* ``dcpgettext``, ``_dcp``

  Translation in specified domain and category with context.

  .. code-block:: jinja
  
      {{ dcpgettext('context', 'domain', 'String', 'category') }}
      {{ _dcp('context', 'domain', 'String', 'category') }}


* ``dcpfgettext``, ``_dcpf``

  Translation in specified domain and category with context and string formatting.

  .. code-block:: jinja
  
      {{ dcpfgettext('context', 'domain', 'String', 'category', arg, ..) }}
      {{ _dcpf('context', 'domain', 'String', 'category', arg, ..) }}

* ``dcngettext``, ``_dcn``

  Pluralized translation in specified domain and category.

  .. code-block:: jinja
  
      {{ dcngettext('domain', 'Singular', 'Plural', 'category') }}
      {{ _dcn('domain', 'Singular', 'Plural', 'category') }}

* ``dcnfgettext``, ``_dcnf``

  Pluralized translation in specified domain and category with string formatting.

  .. code-block:: jinja
  
      {{ dcnfgettext('domain', 'Singular', 'Plural', 'category', arg, ..) }}
      {{ _dcnf('domain', 'Singular', 'Plural', 'category', arg, ..) }}

* ``dcnpgettext``, ``_dcnp``

  Pluralized translation in specified domain and category with context.

  .. code-block:: jinja
  
      {{ dcnpgettext('context', 'domain', 'Singular', 'Plural', 'category') }}
      {{ _dcnp('context', 'domain', 'Singular', 'Plural', 'category') }}

* ``dcnpfgettext``, ``_dcnpf``

  Pluralized translation in specified domain and category with context and string formatting.

  .. code-block:: jinja
  
      {{ dcnpfgettext('context', 'domain', 'Singular', 'Plural', 'category', arg, ..) }}
      {{ _dcnpf('context', 'domain', 'Singular', 'Plural', 'category', arg, ..) }}


Workflow
--------

I recommend again that you read the `GNU gettext documentation`_ to learn more about the correct workflow when working with translations. Especially when working with distributed translators, coordinating source code which is constantly changing, translations which need to be updated and the timelag between these two parties is more complex than you may think. The workflow in a nutshell though is:

* the programmer prepares source code by wrapping strings in ``gettext`` functions
* the translation coordinator runs the extraction script which extracts strings into ``.pot`` files
* the translation coordinator merges the newly extracted source strings with the latest translated
  ``.po`` files using the `msgmerge`_ utility
  
    * this step is crucial, ``msgmerge`` does a lot of automagic to keep translations and source
      files in sync, study its behavior well
    * you typically want a script that does the merging for each of your target languages automatically,
      since the number of files to merge grows exponentially with each new target language/category/domain
      
* the translation coordinator distributes the updated ``.po`` files to the translators

    * you may use a web based tool like `Pootle`_ or similar commercial products for this

* the translators translate the strings

    * if a translation is unclear, the string should be marked ``fuzzy`` and a comment should be added
    * translators need to choose a tool suited for the job, which helps find untranslated or fuzzy strings
      and which honors and displays the meta information of each string
    * a good local tool is `Poedit`_
    
* the translated files are checked for quality, e.g. whether ``sprintf`` formatted strings are still correct

    * if clarification is necessary, possibly the source code should be changed to add a comment or context
    * remember that it's important to keep this process repeatable and automated, manual edits to anything
      but the ``msgstr`` and *translator-comment* nodes in the ``.po`` files will be lost during the next
      merge as will ad-hoc communication with translators
      
* the translation coordinator merges the translated files back into the project

    * if the extracted ``.pot`` files have not changed since the ``.po`` files have been sent out, simply
      replacing the ``.po`` files is fine
    * otherwise ``msgmerge`` should be used to merge the translations with the new sources
    * again, you typically want to have a script that automates this
    
* the ``.po`` files are compiled to ``.mo`` files using `msgfmt`_
* rinse, repeat


.. _`gettext`:                   http://www.php.net/gettext
.. _`documentation`:             http://php.net/manual/en/function.gettext.php
.. _`sprintf`:                   http://php.net/sprintf
.. _`GNU gettext documentation`: http://www.gnu.org/software/gettext/manual/gettext.html
.. _`msgmerge`:                  http://www.gnu.org/software/gettext/manual/gettext.html#msgmerge-Invocation
.. _`msgfmt`:                    http://www.gnu.org/software/gettext/manual/gettext.html#msgfmt-Invocation
.. _`Pootle`:                    http://translate.readthedocs.org/projects/pootle/en/latest/index.html
.. _`Poedit`:                    http://www.poedit.net
.. _`Kunststube\\POTools`:       http://github.com/deceze/Kunststube-POTools
