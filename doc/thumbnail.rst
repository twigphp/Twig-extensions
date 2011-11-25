========================
Thumbnail Twig Extension
========================

Twig extension to create thumbnails given a jpeg/gif/png image. This tries to be a replacement
of the sfThumbnailPlugin for Symfony1.x.

The twig function ``{{ thumbnail() }}`` accepts a relative path (to web folder of an image as argument
and tries to create the relative thumbnail in the same folder, based on a given width, height or scale


-------
Example
-------

In your twig template you can use one in these:

::

    <img src="{{ thumbnail(asset("/uploads/image1.jpeg"), {'width': 200, 'height': 140}) }}">
    
- resizes an image to a fixed width and height
- creates the file [web_dir]/uploads/image1_w200_h140.jpeg
  
::

    <img src="{{ thumbnail(asset("/uploads/image1.jpeg"), {'width': 200, 'permissions': 755} }}">

- resizes an image to a fixed width, calculating the height
- applies 755 as permissions to the created thumbnail file
- creates the file [web_dir]/uploads/image1_w200.jpeg
  
::

    <img src="{{ thumbnail(asset("/uploads/image1.jpeg"), {'height': 140, 'quality': 90}) }}">

- resizes an image to a fixed height, calculating the width
- if the image is jpeg, applies a percentage value (90%) to the thumbnail quality (range 0-100)
- creates the file [web_dir]/uploads/image1_h140_q90.jpeg
  
::

    <img src="{{ thumbnail(asset("/uploads/image1.jpeg"), {'scale': 20}) }}">

- resizes an image to a fixed percentage value (20%), calculating the height and the width
- creates the file [web_dir]/uploads/image1_s20.jpeg


------------
Installation
------------

Edit app/config.yml or your bundle config.yml to use the Thumbnail extension::

    services:
        twig.extension.thumbnail:
            class: Twig_Extensions_Extension_Thumbnail
            tags:
                - { name: twig.extension }


-----
Usage
-----

[optional] Use getWebPath() and getAbsolutePath() in your Entity to manage file upload as
described here: http://symfony.com/doc/2.0/cookbook/doctrine/file_uploads.html

The aim is to pass the relative path of the original image from which you want to generate the thumbnail.
It is not necessary to follow that Cookbook recipe, you can do it in the way you prefer.


If you normally would load an image (through the asset function) in this way::

  <img src="{{ asset(entity.webFile) }}">
  
Now you just wrap the asset() function with the thumbnail() function::

  <img src="{{ thumbnail(asset(entity.webFile), [options] }}">

And that's it!

See next chapter for thumbnail() arguments.


---------------------
thumbnail() arguments
---------------------

The declaration of the function is::
  
  function thumbnail($absolute_filename, $options)

Arguments:

``$absolute_filename``: the absolute path of the original image

``$options`` must be one of the following (twig template syntax)::

    { 'width': 200, 'height': 140 }  resize to width of 200px and height of 140px

    { 'width': 200 }                 resize to width of 200px

    { 'height': 140 }                resize to height of 140px

    { 'scale': 20 }                  create a thumbnails with 20% width and height of the original image
 

you can combine one of the above with two optional attributes::

    { 'permissions': 0755 }          apply 755 permissions to the generated thumbnail
    
    { 'quality': 90 }                create a thumbnail with the 90% of the quality (10% compression) of the original image