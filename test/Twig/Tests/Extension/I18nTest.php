<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Tests_Extension_I18nTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getDelimitersTests
     */
    public function testDelimiters($pattern, $expected, $exception = null)
    {
        if ($exception) $this->setExpectedException($exception);

        $ext = new Twig_Extensions_Extension_I18n(array('delimiters' => $pattern));
        $this->assertSame($expected, $ext->getDelimiters());
    }

    public function getDelimitersTests()
    {
        return array(
            array('{}', array('{', '}')),
            array('[]', array('[', ']')),
            array('[|]', array('[', ']')),
            array('[[|]]', array('[[', ']]')),
            array('{{|}}', array('{{', '}}')),
            array('{{||||}}', array('{{', '}}')),
            array('{{}}', array('{{', '}}')),

            array('abcde', null, 'Twig_Error'),
            array('abcd', array('ab', 'cd')),
            array('{{}', null, 'Twig_Error'),

            // Some edge cases
            array('{{|||}}||||', array('{{', '}}')),
            array('||||{{|||}}', array('{{', '}}')),
            array('||{{|||}}||', array('{{', '}}')),
            array('|  |{{ | | |}} | |', array('{{', '}}')),
        );
    }

}

