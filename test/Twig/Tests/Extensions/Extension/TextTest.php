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
class Twig_Tests_Extensions_Extension_Text_Test extends PHPUnit_Framework_TestCase
{
    public function testSlugify()
    {
        $text = new Twig_Extensions_Extension_Text();

        $output = $text->slugify('This is?foo=bar !@£$ a.string_--with--spaces and &5things');
        $this->assertEquals('this-is-foo-bar-a-string_-with-spaces-and-5things', $output);
        // chinese character test
        $output = $text->slugify('This is汉语!@£$ a.string_--with--sPaces and &5things');
        $this->assertEquals('this-is汉语-a-string_-with-spaces-and-5things', $output);
    }
}
