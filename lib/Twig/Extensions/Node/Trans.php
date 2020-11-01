<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Expression\TempNameExpression;
use Twig\Node\Node;
use Twig\Node\PrintNode;

/**
 * Represents a trans node.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class Twig_Extensions_Node_Trans extends Node
{
    /**
     * Twig_Extensions_Node_Trans constructor.
     * @param Node $body
     * @param Node|null $plural
     * @param AbstractExpression|null $count
     * @param Node|null $notes
     * @param $lineno
     * @param null $tag
     */
    public function __construct(
        Node $body,
        Node $plural = null,
        AbstractExpression $count = null,
        Node $notes = null,
        $lineno,
        $tag = null
    ) {
        $nodes = ['body' => $body];
        if (null !== $count) {
            $nodes['count'] = $count;
        }
        if (null !== $plural) {
            $nodes['plural'] = $plural;
        }
        if (null !== $notes) {
            $nodes['notes'] = $notes;
        }

        parent::__construct($nodes, [], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        [$msg, $vars] = $this->compileString($this->getNode('body'));

        if ($this->hasNode('plural')) {
            [$msg1, $vars1] = $this->compileString($this->getNode('plural'));

            $vars = array_merge($vars, $vars1);
        }

        $function = $this->getTransFunction($this->hasNode('plural'));

        if ($this->hasNode('notes')) {
            $message = trim($this->getNode('notes')->getAttribute('data'));

            // line breaks are not allowed cause we want a single line comment
            $message = str_replace(["\n", "\r"], ' ', $message);
            $compiler->write("// notes: {$message}\n");
        }

        if ($vars) {
            $compiler
                ->write('echo strtr('.$function.'(')
                ->subcompile($msg);

            if ($this->hasNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->hasNode('count') ? $this->getNode('count') : null)
                    ->raw(')');
            }

            $compiler->raw('), array(');

            foreach ($vars as $var) {
                if ('count' === $var->getAttribute('name')) {
                    $compiler
                        ->string('%count%')
                        ->raw(' => abs(')
                        ->subcompile($this->hasNode('count') ? $this->getNode('count') : null)
                        ->raw('), ');
                } else {
                    $compiler
                        ->string('%'.$var->getAttribute('name').'%')
                        ->raw(' => ')
                        ->subcompile($var)
                        ->raw(', ');
                }
            }

            $compiler->raw("));\n");
        } else {
            $compiler
                ->write('echo '.$function.'(')
                ->subcompile($msg);

            if ($this->hasNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->hasNode('count') ? $this->getNode('count') : null)
                    ->raw(')');
            }

            $compiler->raw(");\n");
        }
    }

    /**
     * @param Node $body A Twig_Node instance
     *
     * @return array
     */
    protected function compileString(Node $body): array
    {
        if ($body instanceof NameExpression || $body instanceof ConstantExpression || $body instanceof TempNameExpression) {
            return [$body, []];
        }

        $vars = [];
        if (count($body)) {
            $msg = '';

            foreach ($body as $node) {
                if ($node instanceof PrintNode) {
                    $n = $node->getNode('expr');
                    while ($n instanceof FilterExpression) {
                        $n = $n->getNode('node');
                    }
                    $msg .= sprintf('%%%s%%', $n->getAttribute('name'));
                    $vars[] = new NameExpression($n->getAttribute('name'), $n->getTemplateLine());
                } else {
                    $msg .= $node->getAttribute('data');
                }
            }
        } else {
            $msg = $body->getAttribute('data');
        }

        return [new Node([new ConstantExpression(trim($msg), $body->getTemplateLine())]), $vars];
    }

    /**
     * @param bool $plural Return plural or singular function to use
     *
     * @return string
     */
    protected function getTransFunction($plural)
    {
        return $plural ? 'ngettext' : 'gettext';
    }
}

class_alias('Twig_Extensions_Node_Trans', 'Twig\Extensions\Node\TransNode', false);
