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
    private $complex_vars = false;

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
            $this->complex_vars = $this->extension->getComplexVars();
        }

        $compiler->addDebugInfo($this);

        $vars = array();
        list($msg, $vars) = $this->compileString($this->getNode('body'), $vars);

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
                    ->raw(', abs(')
                    ->subcompile($this->getNode('count'))
                    ->raw(')')
                ;
            }

            $compiler->raw('), array(');

            foreach ($vars as $name => $var) {
                if (!$this->complex_vars) {
                    $name = $var->getAttribute('name');
                }

                if (('count' === $name) && $this->getNode('plural')) {
                    $compiler
                        ->string($this->delims[0].'count'.$this->delims[1])
                        ->raw(' => abs(')
                        ->subcompile($this->getNode('count'))
                        ->raw('), ')
                    ;
                } else {
                    $compiler
                        ->string($this->delims[0].$name.$this->delims[1])
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
    protected function compileString(Twig_Node $body, array $vars)
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
                    if ($this->complex_vars) {
                        $expr = $node->getNode('expr');
                        $name = $this->guessNameFromExpression($expr);
                        $unique = $this->makeUnique($vars, $name, $expr);

                        $msg .= sprintf('%s%s%s', $this->delims[0], $unique, $this->delims[1]);
                        $vars[$unique] = $expr;
                    } else {
                        $n = $node->getNode('expr');
                        while ($n instanceof Twig_Node_Expression_Filter) {
                            $n = $n->getNode('node');
                        }
                        $msg .= sprintf('%s%s%s', $this->delims[0], $n->getAttribute('name'), $this->delims[1]);
                        $vars[] = new Twig_Node_Expression_Name($n->getAttribute('name'), $n->getLine());
                    }
                } else {
                    $msg .= $node->getAttribute('data');
                }
            }
        } else {
            $msg = $body->getAttribute('data');
        }

        return array(new Twig_Node(array(new Twig_Node_Expression_Constant($this->normalize ? $this->normalizeString($msg) : trim($msg), $body->getLine()))), $vars);
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
     * @param string $msg The message string to be normalized.
     * @param string $glue The character used to replace spaces.
     * @return string The normalized string.
     *
     */
    protected function normalizeString($msg, $glue = ' ')
    {
        return preg_replace('/\s+/u', $glue, trim($msg));
    }

    /**
     * Guesses a name from a Twig_Node_Expression. First, we try to get the
     * propossed name from a 'as("name")' filter. If there is no 'as' filter,
     * try to guess a name from the expression itself.
     *
     * @param Twig_Node $expr Expression to guess name from.
     * @return string Proposed name.
     */
    protected function guessNameFromExpression(\Twig_Node $expr)
    {
        // Traverse the Expression AST tree trying to guess a name from 'as'
        // filters, until we reach a non-filter expression.
        while ($expr instanceof Twig_Node_Expression_Filter) {
            // OK, we are still inside a filter, let's look if we got a 'as'.
            if ($expr instanceof Twig_Extensions_Filter_As) {
                // 'as' filters always have to have at least one argument and
                // it is a Constant Expression, guarranteed by the parser.
                return $expr->getNode('arguments')->getNode(0)->getAttribute('value');
            }
            $expr = $expr->getNode('node');
        }

        // We are now at the AST tree for the main expression. We'll try to
        // mangle the name from the rest of this nodes.
        return implode('_', $this->extractNames($expr));
    }

    /**
     * Guesses a name from a series of Twig_Expression nodes. If the expression
     * is too complex to guess a name, it throws a Twig_Error_Syntax.
     *
     * @param Twig_Node $node The starting node to extract names from.
     * @return array Array of names guessed up to this node.
     * @throws Twig_Error_Syntax
     */
    protected function extractNames(\Twig_Node $node)
    {
        switch (get_class($node)) {
            case 'Twig_Node_Expression_GetAttr':
                return array_merge(
                    $this->extractNames($node->getNode('node')),
                    $this->extractNames($node->getNode('attribute'))
                );

            case 'Twig_Node_Expression_Name':
            case 'Twig_Node_Expression_TempName':
                return array($node->getAttribute('name'));

            case 'Twig_Node_Expression_Constant':
                // Constants may have spaces in it. Normalize it!
                return array($this->normalizeString($node->getAttribute('value'), '_'));

            default:
                throw new Twig_Error_Syntax('Sorry, the expression is too complex to use as "trans" value as is. Please use an "as" filter.', $node->getLine());
        }
    }

    /**
     * Makes a variable name unique by adding a serial number if the variable
     * name already exists and its expressions are different. In other words,
     * we only add serial numbers to variables who uses different filters, etc.
     *
     * @param array $vars The existing variables array
     * @param string $name The proposed new name
     * @param TwigNode $expr The expression for this variable
     * @return string The new unique name
     */
    protected function makeUnique($vars, $name, $expr)
    {
        // Loop through until we get a free name. Note that the starting index
        // is "2" instead of "1". This gets us a good looking name series like
        // "name", "mame_2",... It would be ugly to have "name" and "name_1"
        $index = 2;
        $new_name = $name;
        while (array_key_exists($new_name, $vars) && ($vars[$new_name] != $expr)) {
            $new_name = $name.'_'.$index++;
        }

        return $new_name;
    }

}
