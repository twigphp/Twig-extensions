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
 * @package    Twig
 * @subpackage Twig-extensions
 * @author     Alexey Buyanow <alexbuyanow@gmail.com>
 *
 */
class Twig_Extensions_Node_While extends Twig_Node
{
    /**
     * @param Twig_Node_Expression $condition
     * @param Twig_NodeInterface   $body
     * @param int                  $lineNumber
     * @param null                 $tag
     */
    public function __construct(Twig_Node_Expression $condition, Twig_NodeInterface $body, $lineNumber, $tag = null)
    {
        parent::__construct(array('condition' => $condition, 'body' => $body, ), array(), $lineNumber, $tag);
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
            ->subcompile($this->getNode('condition'))
            ->write(") {\n")
            ->indent()
            ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("}\n");
    }
}
