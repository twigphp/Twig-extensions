<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Extensions_Extension_BitwiseShift extends Twig_Extension
{
    /**
     * Returns a list of operators to add to the existing list.
     *
     * @return array An array of operators
     */
    public function getOperators()
    {
        return array(
            array(),
            array(
                'shl' => array('precedence' => 200, 'class' => 'Twig_Extensions_Node_Shl', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'shr' => array('precedence' => 200, 'class' => 'Twig_Extensions_Node_Shr', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
            ),
        );;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'bitwise_shift';
    }
}
