The ImgAlt Extension
===================

The ImgAlt extension provide the following filter:

* ``imgAlt``

This filter is used to fill the alt="" of an image, it use php pathinfo() function to get the image name, and return a string with all keyword longer than the optional ``minimalLengh`` parameter.

.. code-block:: jinja
<img src="{{ imageSrc }}" alt="{{ imageSrc|imgalt }}">

{# Keeping only the words with minimum 6 letters #}
<img src="{{ imageSrc }}" alt="{{ imageSrc|imgalt(6) }}">

.. note::
 All numbers are deleted by default, to clean up image name like : keyword-keyword-keyword-1.jpg, keyword-keyword-keyword-2.jpg...


Installation
------------

First, :ref:`install the Extensions library<extensions-install>`. Next, add
the extension to Twig::

    $twig->addExtension(new Twig_Extensions_Extension_imgAlt());

Usage
-----

Optional argument
-----------------

* ``minimalLenght``: The minimal lenght of word to keep to build the alt string. Default 4
