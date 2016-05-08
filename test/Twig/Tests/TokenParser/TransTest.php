<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_TokenParser_TransTest extends PHPUnit_Framework_TestCase
{
    private $simple_env;
    private $complex_env;

    public function setUp()
    {
        $this->simple_env = new Twig_Environment(new Twig_Loader_Array(array()));
        $this->simple_env->addExtension(new Twig_Extensions_Extension_I18n());

        $this->complex_env = new Twig_Environment(new Twig_Loader_Array(array()));
        $this->complex_env->addExtension(new Twig_Extensions_Extension_I18n(array('complex_vars' => true)));
    }

    /**
     * Test original behaviour is preserved. Simple vars are parsed.
     */
    public function testParsesSimpleVars()
    {
        $parsed = $this->parseSource('{% trans %}Hey {{name}}!{% endtrans %}', $this->simple_env);

        // Kind of dummy assertion. Since exception is not thrown,test already passes.
        $this->assertInstanceOf('Twig_Node_Module', $parsed);
    }

    /**
     * Test original behaviour is preserved. Complex vars throws.
     */
    public function testDoesNotParseComplexVars()
    {
        $this->setExpectedException('Twig_Error_Syntax');

        $parsed = $this->parseSource('{% trans %}Hey {{user.name}}!{% endtrans %}', $this->simple_env);
    }

    /**
     * Complex vars are parsed when 'complex_vars' config is enabled.
     */
    public function testDoesNotThrowForComplexVarsWhenActive()
    {
        $parsed = $this->parseSource('{% trans %}Hey {{user.name}}!{% endtrans %}', $this->complex_env);

        // Kind of dummy assertion. Since exception is not thrown,test already passes.
        $this->assertInstanceOf('Twig_Node_Module', $parsed);
    }

    /**
     * Parses a Twig source. Returns the parsed Node tree.
     *
     * @param string $source The template source.
     * @param Twig_Environment $environment The environment to use.
     * @return Twig_Node_Module The parsed template.
     */
    private function parseSource($source, $environment)
    {
        $lexer = new Twig_Lexer($environment);
        $parser = new Twig_Parser($environment);
        return $parser->parse($lexer->tokenize($source));
    }

}
