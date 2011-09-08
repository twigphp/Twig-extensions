<?php

class Twig_Extensions_TokenParser_Cache extends Twig_TokenParser
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
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        if (!$stream->test(Twig_Token::BLOCK_END_TYPE)) {
            $cache_key = $this->parser->getExpressionParser()->parseExpression();
        } else {
            $file = $stream->getFilename();
            $cache_key = $file . ':' . $lineno;
            $cache_key = new Twig_Node_Expression_Constant($cache_key, $lineno);
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new Twig_Extensions_Node_Cache(
            $body,
            array(
                'cache_key' => $cache_key
            ),
            $lineno,
            $this->getTag()
        );
    }

    public function decideBlockEnd(Twig_Token $token)
    {
        return $token->test('endcache');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    public function getTag()
    {
        return 'cache';
    }
}
