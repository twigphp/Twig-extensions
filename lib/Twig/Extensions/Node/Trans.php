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
    private $extension = null;

    private $delims = array('%', '%');
    private $normalize = false;

    public function __construct(Twig_Node $body, Twig_Node $plural = null, Twig_Node_Expression $count = null, Twig_Node $notes = null, $lineno, $tag = null)
    {
        parent::__construct(array('count' => $count, 'body' => $body, 'plural' => $plural, 'notes' => $notes), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        // Reset default configuration from current environment
        if (is_null($this->extension) && $compiler->getEnvironment()->hasExtension('i18n')) {
            $this->extension = $compiler->getEnvironment()->getExtension('i18n');

            $this->delims = $this->extension->getDelimiters();
            $this->normalize = $this->extension->getNormalize();
        }

        $compiler->addDebugInfo($this);

        list($msg, $vars) = $this->compileString($this->getNode('body'));

        if (null !== $this->getNode('plural')) {
            list($msg1, $vars1) = $this->compileString($this->getNode('plural'));

            $vars = array_merge($vars, $vars1);
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
                    ->raw(', abs(')
                    ->subcompile($this->getNode('count'))
                    ->raw(')')
                ;
            }

            $compiler->raw('), array(');

            foreach ($vars as $var) {
                if ('count' === $var->getAttribute('name')) {
                    $compiler
                        ->string($this->delims[0].'count'.$this->delims[1])
                        ->raw(' => abs(')
                        ->subcompile($this->getNode('count'))
                        ->raw('), ')
                    ;
                } else {
                    $compiler
                        ->string($this->delims[0].$var->getAttribute('name').$this->delims[1])
                        ->raw(' => ')
                        ->subcompile($var)
                        ->raw(', ')
                    ;
                }
            }

            $compiler->raw("));\n");
        } else {
            $compiler
                ->write('echo '.$function.'(')
                ->subcompile($msg)
            ;

            if (null !== $this->getNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->getNode('count'))
                    ->raw(')')
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
    protected function compileString(Twig_Node $body)
    {
        if ($body instanceof Twig_Node_Expression_Name || $body instanceof Twig_Node_Expression_Constant || $body instanceof Twig_Node_Expression_TempName) {
            return array($body, array());
        }

        $vars = array();
        if (count($body)) {
            $msg = '';

            foreach ($body as $node) {
                if (get_class($node) === 'Twig_Node' && $node->getNode(0) instanceof Twig_Node_SetTemp) {
                    $node = $node->getNode(1);
                }

                if ($node instanceof Twig_Node_Print) {
                    $n = $node->getNode('expr');
                    while ($n instanceof Twig_Node_Expression_Filter) {
                        $n = $n->getNode('node');
                    }
                    $msg .= sprintf('%s%s%s', $this->delims[0], $n->getAttribute('name'), $this->delims[1]);
                    $vars[] = new Twig_Node_Expression_Name($n->getAttribute('name'), $n->getLine());
                } else {
                    $msg .= $node->getAttribute('data');
                }
            }
        } else {
            $msg = $body->getAttribute('data');
        }

        return array(new Twig_Node(array(new Twig_Node_Expression_Constant($this->normalize ? $this->normalize($msg) : trim($msg), $body->getLine()))), $vars);
    }

    /**
     * Normalizes a string (removes spaces inside the string)
     *
     * Why?: For large translatable strings or strings spanning multiple lines,
     * it is necessary to normalize the string so that translators doesn't get
     * carriage returns or tab characters inside the string. Normalization is
     * also needed to mantain the integrity of multiline strings and the
     * indentation of the source file changed. Take for example:
     *
     *  Before (A):
     *
     *      {% trans %}
     *          This is a translatable string
     *          spanning multiple lines
     *      {% endtrans %}
     *
     *  After (B):
     *
     *      {% if some_condition %}
     *          {% trans %}
     *              This is a translatable string
     *              spanning multiple lines
     *          {% endtrans %}
     *      {% endif %}
     *
     * If the above example, we needed to add some condition and thus added
     * indentation because of the "if". Then, the translatable string changes
     * and to Gettext it is a different string because of the different spaces
     * inside the sentence.
     *
     * @param string $msg The message string to be normalized
     * @return string
     *
     */
    private function normalize($msg)
    {
        return trim(preg_replace('/\s+/u', ' ', $msg));
    }

}
