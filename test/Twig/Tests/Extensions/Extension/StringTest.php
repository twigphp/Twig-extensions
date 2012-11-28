<?php

/**
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Chris Sedlmayr (catchamonkey) <chris@sedlmayr.co.uk>
 * @package Twig
 */
class Twig_Tests_Extensions_Extension_String_Test extends PHPUnit_Framework_TestCase
{
    public function testSlugify()
    {
        $string = new Twig_Extensions_Extension_String();

        $output = $string->slugify('This is?foo=bar !@£$ a.string_--with--spaces and &5things');
        $this->assertEquals('this-is-foo-bar-a-string_-with-spaces-and-5things', $output);
        // chinese character test
        $output = $string->slugify('This is汉语!@£$ a.string_--with--sPaces and &5things');
        $this->assertEquals('this-is汉语-a-string_-with-spaces-and-5things', $output);
    }
}
