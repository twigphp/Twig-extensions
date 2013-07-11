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
 * While loop
 *
 * Usage:
 *  {% while <condition>%}
 *     <!-- loop body -->
 *  {% endwhile %}
 *
 * Example:
 *  {% while test_var == 'true' %}
 *  {% endwhile %}
 *
 */
class Twig_Extensions_TokenParser_While extends Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
        $lineNumber = $token->getLine();
        $expr       = $this->parser->getExpressionParser()->parseExpression();
        $stream     = $this->parser->getStream();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideWhileEnd'));

        $tests = array($expr, $body);
        $stream->next();

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new Twig_Extensions_Node_While(new Twig_Node($tests), $lineNumber, $this->getTag());
    }

    /**
     * Block end
     *
     * @param Twig_Token $token
     *
     * @return bool
     */
    public function decideWhileEnd(Twig_Token $token)
    {
        return $token->test(array('endwhile'));
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'while';
    }
}
