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
 * Represents a trans node.
 *
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class Twig_Extensions_Node_Trans extends Twig_Node
{
    public function __construct(Twig_Node $body, Twig_Node_Expression_Array $with = null, Twig_Node $plural = null, Twig_Node_Expression $count = null, Twig_Node $notes = null, $lineno, $tag = null)
    {
        parent::__construct(array('count' => $count, 'body' => $body, 'with' => $with, 'plural' => $plural, 'notes' => $notes), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        list($msg, $vars) = $this->compileString($this->getNode('body'), $this->getNode('with'));

        if (null !== $this->getNode('plural')) {
            list($msg1, $vars) = $this->compileString($this->getNode('plural'), $vars);
        }

        $function = null === $this->getNode('plural') ? 'gettext' : 'ngettext';

        if (null !== $notes = $this->getNode('notes')) {
            $message = trim($notes->getAttribute('data'));

            // line breaks are not allowed cause we want a single line comment
            $message = str_replace(array("\n", "\r"), ' ', $message);
            $compiler->write("// notes: {$message}\n");
        }

        if ($vars) {
            $compiler
                ->write('echo strtr('.$function.'(')
                ->subcompile($msg)
            ;

            if (null !== $this->getNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', ')
                    ->subcompile($this->getNode('count'))
                ;
            }

            $compiler
                ->raw('), ')
                ->subcompile($vars)
                ->raw(");\n")
            ;
        } else {
            $compiler
                ->write('echo '.$function.'(')
                ->subcompile($msg)
            ;

            if (null !== $this->getNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', ')
                    ->subcompile($this->getNode('count'))
                ;
            }

            $compiler->raw(");\n");
        }
    }

    /**
     * @param Twig_Node $body A Twig_Node instance
     *
     * @return array
     */
    protected function compileString(Twig_Node $body, $vars = null, $ignoreStrictCheck = false)
    {
        if ($body instanceof Twig_Node_Expression_Name || $body instanceof Twig_Node_Expression_Constant || $body instanceof Twig_Node_Expression_TempName) {
            return array($body, array());
        }

        if (count($body)) {
            $msg = '';

            foreach ($body as $node) {
                if (get_class($node) === 'Twig_Node' && $node->getNode(0) instanceof Twig_Node_SetTemp) {
                    $node = $node->getNode(1);
                }

                if ($node instanceof Twig_Node_Print) {
                    if (null === $vars) {
                        $vars = new Twig_Node_Expression_Array(array(), null);
                    }

                    $n = $node->getNode('expr');
                    while ($n instanceof Twig_Node_Expression_Filter) {
                        $n = $n->getNode('node');
                    }
                    $msg .= sprintf('%%%s%%', $n->getAttribute('name'));

                    $key = new Twig_Node_Expression_Constant('%'.$n->getAttribute('name').'%', $body->getLine());

                    if (!$vars->hasElement($key)) {
                        if ('count' == $n->getAttribute('name') && null !== $this->getNode('count')) {
                            $vars->addElement($this->getNode('count'), $key);
                        } else {
                            $varExpr = new Twig_Node_Expression_Name($n->getAttribute('name'), $body->getLine());
                            $varExpr->setAttribute('ignore_strict_check', $ignoreStrictCheck);
                            $vars->addElement($varExpr, $key);
                        }
                    }
                } else {
                    $msg .= $node->getAttribute('data');
                }
            }
        } else {
            $msg = $body->getAttribute('data');
        }

        if (null !== $vars) {
            preg_match_all('/(?<!%)%([^%]+)%/', $msg, $matches);

            foreach ($matches[1] as $var) {
                $key = new Twig_Node_Expression_Constant('%'.$var.'%', $body->getLine());
                if (!$vars->hasElement($key)) {
                    $varExpr = new Twig_Node_Expression_Name($var, $body->getLine());
                    $varExpr->setAttribute('ignore_strict_check', $ignoreStrictCheck);
                    $vars->addElement($varExpr, $key);
                }
            }
        }

        return array(new Twig_Node(array(new Twig_Node_Expression_Constant(trim($msg), $body->getLine()))), $vars);
    }
}
