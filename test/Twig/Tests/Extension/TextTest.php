<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


// I wonder if there is a cleaner way to do this, autoload didn't work for me 
require_once dirname(__FILE__).'/../../../../lib/Twig/Extensions/Extension/Text.php';

class Twig_Extensions_Extension_TextTest extends PHPUnit_Framework_TestCase{

		public function testURLisWrappedByAnAnchorTag()
    {
        $text          = 'www.google.es';
        $expected_text = '<a href="http://www.google.es" target="_blank" >www.google.es</a>';
        $filtered_text = auto_link_filter($text);

        $this->assertEquals($expected_text, $filtered_text);
    }

    public function testAnchorsTagNotAccepted()
    {
        $text          = '<a href="http://www.google.es" target="_blank" >www.yahoo.es</a>';
        $expected_text = '<a href="http://www.google.es" target="_blank" >www.yahoo.es</a>';
        $filtered_text = auto_link_filter($text);

        $this->assertEquals($expected_text, $filtered_text);
    }

    public function testURLinParagraphIsConvertedToAnchorTag()
    {
        $text          = <<<HTML
        <p>Lorem ipsum dolor et sit www.google.es con amet</p>
HTML;

        $expected_text = <<<HTML
        <p>Lorem ipsum dolor et sit <a href="http://www.google.es" target="_blank" >www.google.es</a> con amet</p>
HTML;

        $filtered_text = auto_link_filter($text);

        $this->assertEquals($expected_text, $filtered_text);

    }

}
