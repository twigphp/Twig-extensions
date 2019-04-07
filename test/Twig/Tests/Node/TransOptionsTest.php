<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Node_TransOptionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     */
    public function testConstructorReturnsDefaultOptions()
    {
        $options = new Twig_Extensions_Node_Trans_Options();

        $this->assertSame(array('%', '%'), $options->getDelimiters());
        $this->assertSame(false, $options->getNormalize());
        $this->assertSame(false, $options->getComplexVars());
    }

    /**
     *
     */
    public function testAcceptsDelimitersOption()
    {
        $options = new Twig_Extensions_Node_Trans_Options(array(
            "delimiters" => "{{}}"
        ));

        $this->assertSame(array('{{', '}}'), $options->getDelimiters());
    }

    /**
     *
     */
    public function testAcceptsNormalizeOption()
    {
        $options = new Twig_Extensions_Node_Trans_Options(array(
            "normalize" => true
        ));

        $this->assertSame(true, $options->getNormalize());
    }

    /**
     *
     */
    public function testAcceptsComplexVarsOption()
    {
        $options = new Twig_Extensions_Node_Trans_Options(array(
            "complex_vars" => true
        ));

        $this->assertSame(true, $options->getComplexVars());
    }

    /**
     * @dataProvider delimiterTestProvider
     */
    public function testDelimiters($pattern, $expected, $exception = null)
    {
        if ($exception) $this->setExpectedException($exception);

        $ops = new Twig_Extensions_Node_Trans_Options(array('delimiters' => $pattern));
        $this->assertSame($expected, $ops->getDelimiters());
    }

    public function delimiterTestProvider()
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
