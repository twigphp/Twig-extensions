<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extensions_Filter_As extends Twig_Node_Expression_Filter
{
    public function __construct(Twig_Node $node, Twig_Node_Expression_Constant $filterName, Twig_Node $arguments, $lineno, $tag = null)
    {
        if (($arguments->count() != 1) || (!$arguments->getNode(0) instanceof Twig_Node_Expression_Constant)) {
            throw new Twig_Error_Syntax('Invalid parameters to "as" filter.', $lineno);
        }

        parent::__construct($node, $filterName, $arguments, $lineno, $tag);
    }

    /**
     * Compile an "As" filter for I18n variable alias.
     * This is a dummy filter, so it just need to pass compilation to its node.
     */
    public function compile(Twig_Compiler $compiler)
    {
        $this->getNode('node')->compile($compiler);
    }
}
