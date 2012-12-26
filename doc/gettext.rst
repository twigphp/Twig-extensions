The Gettext Extension
=====================

The Gettext extension adds complete `gettext`_ support to Twig. It defines a whole host of functions and filters that can be used in Twig templates. It also adds an ``Extractor`` class, which can parse Twig templates and extract all strings marked for localization. The gettext support includes:

* categories
* domains
* context
* plurals
* extractable comments

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
    
The ``Twig_Extensions_Extension_Gettext`` class accepts one constructor argument: ``$useShortnames``. By default it is set to ``true``, which means every gettext function and filter is aliased to a shortname. If this conflicts with another extension, set it to false::

    $twig->addExtension(new Twig_Extensions_Extension_Gettext(false));

Most examples below assume shortnames are on, see :ref:`API documentation <api-docs>` for alternative names.

    
Usage
-----

Wrap any translatable string in your templates into one of the appropriate gettext functions or filters:

.. code-block:: jinja

    <h1>{{ _('Hello World!') }}</h1>
    <h1>{{ 'Hello World!'|gettext }}</h1>
    
    <p>{{ _n('One day without accident.', '%d days without accident.', n)|format(n) }}</p>
    
    {#
       The %s is an noun, the %d a number. If you need to
       switch the order of the placeholders for translation,
       use %1$s and %2$d instead.
    #}
    <p>{{ 'The %s contains %d monkeys'|gettext|format(thing, num) }}</p>
    
    <input type="submit" value="{{ 'Update'|_p('verb') }}">
    
    {% if someError %}
        <p>{{ 'Some error occurred!'|_d('errors') }}</p>
    {% endif %}
    
.. caution::

    You can use gettext functions only for constant expressions. Using them on a variable or dynamic expression
    means the string cannot be automatically extracted, which breaks the workflow of gettext. The bundled ``Extractor``
    class will throw an error if it encounters invalid values. For example, this does not work:
    
    .. code-block:: jinja
    
        {{ 'string'|title|gettext }}
        {{ var|gettext }}
    
Strings can either be wrapped in a gettext function or can be put trough an equivalent filter. The function syntax may be better for users already familiar with the PHP gettext extension and/or may be more compatible with existing toolchains, the filter syntax respects the Twig syntax better. All gettext expressions either start with an underscore (short aliases) or end in ``gettext`` (full name). There are many variations of the basic ``_()``/``gettext()`` function, each with an array of letters tagged on. The letters are shorthand for various attributes that can be modified about the translatable string:

* ``n``: pluralization
* ``d``: override default domain
* ``c``: override ``LC_MESSAGES`` category
* ``p``: put the string in a context

The full `GNU gettext documentation`_ details the usage of these different attributes and I highly recommend you read it. A short summary follows.

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
    <p>{{ "Get Foobar'd today!"|gettext }}</p>
    
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

Note that the argument order may differ between function and filter syntax. The function syntax uses the original PHP/C gettext parameter order, while the filter syntax tries to make the argument order more memorable by using largely the same order as the n/d/c/p letters in the name of the filter.

* ``gettext``, ``_``

  Basic translation in default domain and ``LC_MESSAGES`` category.

  .. code-block:: jinja
  
      {{ 'String'|gettext }}
      {{ 'String'|_ }}
      {{ gettext('String') }}
      {{ _('String') }}

* ``pgettext``, ``_p``

  Translation in default domain and ``LC_MESSAGES`` category with context.

  .. code-block:: jinja
  
      {{ 'String'|pgettext('context') }}
      {{ 'String'|_p('context') }}
      {{ pgettext('context', 'String') }}
      {{ _p('context', 'String') }}

* ``ngettext``, ``_n``

  Pluralized translation in default domain and ``LC_MESSAGES`` category.

  .. code-block:: jinja
  
      {{ 'Singular'|ngettext('Plural', num) }}
      {{ 'Singular'|_n('Plural', num) }}
      {{ ngettext('Singular', 'Plural', num) }}
      {{ _n('Singular', 'Plural', num) }}

* ``npgettext``, ``_np``

  Pluralized translation in default domain and ``LC_MESSAGES`` category with context.

  .. code-block:: jinja
  
      {{ 'Singular'|npgettext('Plural', num, 'context') }}
      {{ 'Singular'|_np('Plural', num, 'context') }}
      {{ npgettext('context', 'Singular', 'Plural', num) }}
      {{ _np('context', 'Singular', 'Plural', num) }}

* ``dgettext``, ``_d``

  Translation in ``LC_MESSAGES`` category and specified domain.

  .. code-block:: jinja
  
      {{ 'String'|dgettext('domain') }}
      {{ 'String'|_d('domain') }}
      {{ dgettext('domain', 'String') }}
      {{ _d('domain', 'String') }}

* ``dpgettext``, ``_dp``

  Translation in ``LC_MESSAGES`` category and specified domain with context.

  .. code-block:: jinja
  
      {{ 'String'|dpgettext('domain', 'context') }}
      {{ 'String'|_dp('domain', 'context') }}
      {{ dpgettext('context', 'domain', 'String') }}
      {{ _dp('context', 'domain', 'String') }}

* ``dngettext``, ``_dn``

  Pluralized translation in ``LC_MESSAGES`` category and specified domain.

  .. code-block:: jinja
  
      {{ 'Singular'|dngettext('Plural', num, 'domain') }}
      {{ 'Singular'|_dn('Plural', num, 'domain') }}
      {{ dngettext('domain', 'Singular', 'Plural', num) }}
      {{ _dn('domain', 'Singular', 'Plural', num) }}

* ``dnpgettext``, ``_dnp``

  Pluralized translation in ``LC_MESSAGES`` category and specified domain with context.

  .. code-block:: jinja
  
      {{ 'Singular'|dnpgettext('Plural', num, 'domain', 'context') }}
      {{ 'Singular'|_dnp('Plural', num, 'domain', 'context') }}
      {{ dnpgettext('context, 'domain', 'Singular', 'Plural', num) }}
      {{ _dnp('context', 'domain', 'Singular', 'Plural', num) }}

* ``dcgettext``, ``_dc``

  Translation in specified domain and category.

  .. code-block:: jinja
  
      {{ 'String'|dcgettext('domain', 'category') }}
      {{ 'String'|_dc('domain', 'category') }}
      {{ dcgettext('domain', 'String', 'category') }}
      {{ _dc('domain', 'String', 'category') }}

* ``dcpgettext``, ``_dcp``

  Translation in specified domain and category with context.

  .. code-block:: jinja
  
      {{ 'String'|dcpgettext('domain', 'category', 'context') }}
      {{ 'String'|_dcp('domain', 'category', 'context') }}
      {{ dcpgettext('context', 'domain', 'String', 'category') }}
      {{ _dcp('context', 'domain', 'String', 'category') }}

* ``dcngettext``, ``_dcn``

  Pluralized translation in specified domain and category.

  .. code-block:: jinja
  
      {{ 'Singular'|dcngettext('Plural', num, 'domain', 'category') }}
      {{ 'Singular'|_dcn('Plural', num, 'domain', 'category') }}
      {{ dcngettext('domain', 'Singular', 'Plural', num, 'category') }}
      {{ _dcn('domain', 'Singular', 'Plural', num, 'category') }}

* ``dcnpgettext``, ``_dcnp``

  Pluralized translation in specified domain and category with context.

  .. code-block:: jinja

      {{ 'Singular'|dcnpgettext('Plural', num, 'domain', 'category', 'context') }}
      {{ 'Singular'|_dcnp('Plural', num, 'domain', 'category', 'context') }}
      {{ dcnpgettext('context', 'domain', 'Singular', 'Plural', 'category') }}
      {{ _dcnp('context', 'domain', 'Singular', 'Plural', 'category') }}


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
.. _`GNU gettext documentation`: http://www.gnu.org/software/gettext/manual/gettext.html
.. _`msgmerge`:                  http://www.gnu.org/software/gettext/manual/gettext.html#msgmerge-Invocation
.. _`msgfmt`:                    http://www.gnu.org/software/gettext/manual/gettext.html#msgfmt-Invocation
.. _`Pootle`:                    http://translate.readthedocs.org/projects/pootle/en/latest/index.html
.. _`Poedit`:                    http://www.poedit.net
.. _`Kunststube\\POTools`:       http://github.com/deceze/Kunststube-POTools
