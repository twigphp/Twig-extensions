<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009-2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


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
        $keyToken = $stream->getCurrent();

        if ($keyToken->test(Twig_Token::STRING_TYPE)) {
            $dynamicKey = false;
        } elseif ($keyToken->test(Twig_Token::NAME_TYPE)) {
            $dynamicKey = true;
        } else {
            // it should notify that actually two kind of tokens may be used:
            // STRING_TYPE and NAME_TYPE
            $keyToken->expect(Twig_Token::STRING_TYPE);
        }

        $key = $keyToken->getValue();
        $stream->next();
        $time = $stream->expect(Twig_Token::NUMBER_TYPE)->getValue();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $value = $this->parser->subparse(array($this, 'decideCacheEnd'), true);
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new Twig_Extensions_Node_Cache($key, $value, $time, $dynamicKey, $lineno, $this->getTag());
    }

    public function decideCacheEnd(Twig_Token $token)
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
