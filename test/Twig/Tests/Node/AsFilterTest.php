<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Node_AsFilterTest extends Twig_Test_NodeTestCase
{
    public function testThrowsAtInvalidArgumentsWhenParsing()
    {
        $this->setExpectedException('Twig_Error_Syntax');

        $env = $this->getEnvironment();
        $lexer = new Twig_Lexer($env);
        $parser = new Twig_Parser($env);
        $parser->parse($lexer->tokenize('{{ user | as(name) }}'));
    }

    public function getEnvironment()
    {
        $env = new Twig_Environment(new Twig_Loader_Array(array()));
        $env->addExtension(new Twig_Extensions_Extension_I18n());

        return $env;
    }

    public function getTests()
    {
        $tests = array();

        $expr = new Twig_Node_Expression_Constant('foo', 1);
        $node = $this->createFilter($expr, 'as', array(new Twig_Node_Expression_Constant('bar', 1)));
        $tests[] = array($node, '"foo"');

        $expr = new Twig_Node_Expression_Constant('foo', 1);
        $node = $this->createFilter($expr, 'as', array(new Twig_Node_Expression_Constant('bar', 1)));
        $node = $this->createFilter($node, 'upper');
        $tests[] = array($node, 'twig_upper_filter($this->env, "foo")');

        $expr = new Twig_Node_Expression_Constant('foo', 1);
        $node = $this->createFilter($expr, 'upper');
        $node = $this->createFilter($node, 'as', array(new Twig_Node_Expression_Constant('bar', 1)));
        $tests[] = array($node, 'twig_upper_filter($this->env, "foo")');

        return $tests;
    }

    protected function createFilter($node, $tag, array $arguments = array())
    {
        $name = new Twig_Node_Expression_Constant($tag, 1);
        $arguments = new Twig_Node($arguments);

        return $tag == 'as'
            ? new Twig_Extensions_Filter_As($node, $name, $arguments, 1)
            : new Twig_Node_Expression_Filter($node, $name, $arguments, 1);
    }

}
