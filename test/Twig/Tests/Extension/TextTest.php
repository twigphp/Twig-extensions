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
    /** @var TwigEnvironment */
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

    public function testDoesNotTruncateInputShorterThanLimit()
    {
        $input = 'We are the knights who say Ni!';
        $output = 'We are the knights who say Ni!';
        $limit = strlen($input) + 42;

        $actual = twig_truncate_filter($this->env, $input, $limit, false, '…');
        $this->assertSame($output, $actual);

        $actual = twig_truncate_filter($this->env, $input, $limit, true, '…');
        $this->assertSame($output, $actual);
    }

    public function testDoesNotTruncateInputEqualToLimit()
    {
        $input = 'We are the knights who say Ni!';
        $output = 'We are the knights who say Ni!';
        $limit = strlen($input);

        $actual = twig_truncate_filter($this->env, $input, $limit, false, '…');
        $this->assertSame($output, $actual);

        $actual = twig_truncate_filter($this->env, $input, $limit, true, '…');
        $this->assertSame($output, $actual);
    }

    public function testTruncatesInputWayLargerThanLimit()
    {
        $input = 'Nobody expects the spanish inquisition';
        $outputUnpreserved = 'Nobody expects the span…';
        $outputPreserved = 'Nobody expects the…';
        $limit = mb_strlen($outputUnpreserved);

        $actual = twig_truncate_filter($this->env, $input, $limit, false, '…');
        $this->assertSame($outputUnpreserved, $actual);

        $actual = twig_truncate_filter($this->env, $input, $limit, true, '…');
        $this->assertSame($outputPreserved, $actual);
    }

    public function testTruncatesInputOneCharacterLargerThanLimit()
    {
        $input = 'Nobody expects the spanish inquisition';
        $outputUnpreserved = 'Nobody expects the spanish inquisi…';
        $outputPreserved = 'Nobody expects the spanish…';
        $limit = strlen($input) - 1;

        $actual = twig_truncate_filter($this->env, $input, $limit, false, '…');
        $this->assertSame($outputUnpreserved, $actual);

        $actual = twig_truncate_filter($this->env, $input, $limit, true, '…');
        $this->assertSame($outputPreserved, $actual);
    }

    public function testPreservesWordWhenRoomLeft()
    {
        $input = 'We are no longer the knights who say ni, we are now the knights...';
        $output = 'We are no longer the knights who say ni, we…';
        $limit = mb_strlen($output);

        $actual = twig_truncate_filter($this->env, $input, $limit, true, '…');
        $this->assertSame($output, $actual);
    }

    public function testDoesNotPreservesWordWhenNoRoomLeft()
    {
        $input = 'No one expects the spanish inquisition';
        $output = 'No one expects the…';
        $limit = mb_strlen($output);

        $actual = twig_truncate_filter($this->env, $input, $limit, true, '…');
        $this->assertSame($output, $actual);
    }
}
