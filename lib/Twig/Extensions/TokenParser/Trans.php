<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Error\SyntaxError;
use Twig\Node\Expression\NameExpression;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Node\TextNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class Twig_Extensions_TokenParser_Trans extends AbstractTokenParser
{
    /**
     * @param Token $token
     * @return ModuleNode
     * @throws SyntaxError
     */
    public function parse(Token $token): Node
    {
        $parser = $this->parser;
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $count = null;
        $plural = null;
        $notes = null;

        if (!$stream->test(Token::BLOCK_END_TYPE)) {
            $body = $this->parser->getExpressionParser()->parseExpression();
        } else {
            $stream->expect(Token::BLOCK_END_TYPE);
            $body = $this->parser->subparse([$this, 'decideForFork']);
            $next = $stream->next()->getValue();

            if ('plural' === $next) {
                $count = $this->parser->getExpressionParser()->parseExpression();
                $stream->expect(Token::BLOCK_END_TYPE);
                $plural = $this->parser->subparse([$this, 'decideForFork']);

                if ('notes' === $stream->next()->getValue()) {
                    $stream->expect(Token::BLOCK_END_TYPE);
                    $notes = $this->parser->subparse([$this, 'decideForEnd'], true);
                }
            } elseif ('notes' === $next) {
                $stream->expect(Token::BLOCK_END_TYPE);
                $notes = $this->parser->subparse([$this, 'decideForEnd'], true);
            }
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        $this->checkTransString($body, $lineno);

        return new Twig_Extensions_Node_Trans($body, $plural, $count, $notes, $lineno, $this->getTag());
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function decideForFork(Token $token): bool
    {
        return $token->test(['plural', 'notes', 'endtrans']);
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function decideForEnd(Token $token): bool
    {
        return $token->test('endtrans');
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'trans';
    }

    /**
     * @param Node $body
     * @param int $lineno
     * @throws SyntaxError
     */
    protected function checkTransString(Node $body, int $lineno): void
    {
        foreach ($body as $i => $node) {
            if (
                $node instanceof TextNode ||
                ($node instanceof PrintNode && $node->getNode('expr') instanceof NameExpression)
            ) {
                continue;
            }

            throw new SyntaxError(
                'The text to be translated with "trans" can only contain references to simple variables',
                $lineno
            );
        }
    }
}

class_alias('Twig_Extensions_TokenParser_Trans', 'Twig\Extensions\TokenParser\TransTokenParser', false);
