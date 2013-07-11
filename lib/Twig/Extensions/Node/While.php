<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a while node.
 *
 * @package    twig
 * @author     Alexey Buyanow <alexbuyanow@gmail.com>
 *
 */
class Twig_Extensions_Node_While extends Twig_Node
{
    /**
     * @param Twig_NodeInterface $tests
     * @param integer            $lineNumber
     * @param string|null        $tag
     */
    public function __construct(Twig_NodeInterface $tests, $lineNumber, $tag = null)
    {
        parent::__construct(array('tests' => $tests,), array(), $lineNumber, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler $compiler
     *
     * @return void
     *
     * @see \Twig_Node::compile
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('while (')
            ->subcompile($this->getNode('tests')->getNode(0))
            ->write(") {\n")
            ->indent()
            ->subcompile($this->getNode('tests')->getNode(1))
            ->outdent()
            ->write("}\n");
    }
}
