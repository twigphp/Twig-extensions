The Date Extension
==================

The ``date`` extension provides the following filters:

* ``datediff``: The date filter is able to format a interval between a date and today::
  
   The post was sent {{ post.published_at | datediff }} ago.

   {# Returns 'The post was sent 5 days and 1 hour ago' #}


Configuration
-------------

This filter uses the traslator service so, you need to activate it::

    // app/config/config.yml
    framework:
        translator:      { fallback: en }

After that, you need to register the extension::

    // app/config/config.yml
    services:
      twig.extension.date:
        class: Twig_Extensions_Extension_Date
        tags:
          - { name: twig.extension }
        arguments: [ @translator ]

Usage
_____

How to use::

    {{ [datetime instance or string] | datediff }}

You can also specify the timezone, the message catalog and the locale::

    {{ [datetime instance or string] | datediff("Europe/Madrid")  }}
    {{ [datetime instance or string] | datediff("Europe/Madrid", "admin")  }}

By default, the message catalog is "TwigExtensionsDate". There are some 
translations on vendor/twig-extensions/lib/Resources/translations/ You have 
to copy this files to your bundle on src/Your/Bundle/Resources/translations/



 