<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Tests_Extension_TextTest extends PHPUnit_Framework_TestCase
{
    /** @var Twig_Environment */
    private $env;

    public static function setUpBeforeClass()
    {
        if (!class_exists('Twig_Extensions_Extension_Text')) {
            self::markTestSkipped('Unable to find class Twig_Extensions_Extension_Text.');
        }
    }

    public function setUp()
    {
        $this->env = $this->getMock('Twig_Environment');
        $this->env
            ->expects($this->any())
            ->method('getCharset')
            ->will($this->returnValue('utf-8'))
        ;
    }

    /**
     * @expectedException Twig_Error_Syntax
     */
    public function testSeparatorIsLongerThanString()
    {
        twig_truncate_filter($this->env, 'This is a very long sentence.', 2, true, '...');
    }

    /**
     * @dataProvider getTruncateTestData
     */
    public function testTruncate($input, $length, $preserve, $separator, $expectedOutput)
    {
        $output = twig_truncate_filter($this->env, $input, $length, $preserve, $separator);
        $this->assertEquals($expectedOutput, $output);
    }

    public function getTruncateTestData()
    {
        return array(
            array('This is a very long sentence.', 3, false, '...', '...'),
            array('This is a very long sentence.', 6, false, '...', 'Thi...'),
            array('This is a very long sentence.', 7, true, '...', 'This...'), // Character after $limit is a whitespace.
            array('This is a very long sentence.', 7, true, '[..x..]', '[..x..]'), // Separator is the same length as $limit.
            array('This is a very long sentence.', 23, false, '...', 'This is a very long...'),
            array('This is a very long sentence.', 23, true, '...', 'This is a very long...'), // $limit is right on the last word.
            array('This is a very long sentence.', 28, true, '...', 'This is a very long...'), // $limit is right before the last character, but last char is not a whitespace.
            array('This is a very long sentence.', 29, true, '...', 'This is a very long sentence.'), // $limit is as long as the string.
            array('This is a very long sentence.', 30, true, '...', 'This is a very long sentence.'), // $limit is longer than the string.
            array('This is a very long sentence.', 15, true, '[..xXOXx..]', 'This[..xXOXx..]'), // Separator covers multiple words.
            array('Short one.', 30, true, '[..xXXx..]', 'Short one.'), // Separator is as long as the whole string
        );
    }
}
