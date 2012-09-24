<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Text_linkifyTest extends PHPUnit_Framework_TestCase
{
    public function testLinkify()
    {
        $textFilter = new Twig_Extensions_Text();
		$link = 'http://www.microsoft.com/';
        $this->assertEquals('<a href="'.$link.'">'.$link.'</a>', $textFilter->linkify($link));
    }
}
