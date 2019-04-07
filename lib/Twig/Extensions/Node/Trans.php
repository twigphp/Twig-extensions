<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Node\Expression;

/**
 * Represents a trans node.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class Twig_Extensions_Node_Trans extends Twig_Node
{
    /**
     * Holds the current options from I18n extension.
     *
     * @see Twig_Extensions_Node_Trans_Options
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function __construct(Twig_Node $body, Twig_Node $plural = null, Twig_Node_Expression $count = null, Twig_Node $notes = null, $lineno, $tag = null, $options = null)
    {
        $nodes = array('body' => $body);
        if (null !== $count) {
            $nodes['count'] = $count;
        }
        if (null !== $plural) {
            $nodes['plural'] = $plural;
        }
        if (null !== $notes) {
            $nodes['notes'] = $notes;
        }
        if (null !== $options) {
            $this->options = $options;
        } else {
            $this->options = new Twig_Extensions_Node_Trans_Options();
        }

        parent::__construct($nodes, array(), $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $delims = $this->options->getDelimiters();
        $complex = $this->options->getComplexVars();

        $vars = array();
        list($msg, $vars) = $this->compileString($this->getNode('body'), $vars);

        if ($this->hasNode('plural')) {
            list($msg1, $vars) = $this->compileString($this->getNode('plural'), $vars);
        }

        $function = $this->getTransFunction($this->hasNode('plural'));

        if ($this->hasNode('notes')) {
            $message = trim($this->getNode('notes')->getAttribute('data'));

            // line breaks are not allowed cause we want a single line comment
            $message = str_replace(array("\n", "\r"), ' ', $message);
            $compiler->write("// notes: {$message}\n");
        }

        if ($vars) {
            $compiler
                ->write('echo strtr('.$function.'(')
                ->subcompile($msg)
            ;

            if ($this->hasNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->hasNode('count') ? $this->getNode('count') : null)
                    ->raw(')')
                ;
            }

            $compiler->raw('), array(');

            foreach ($vars as $name => $var) {
                if (!$complex) {
                    $name = $var->getAttribute('name');
                }

                if (('count' === $name) && $this->hasNode('plural')) {
                    $compiler
                        ->string($delims[0].'count'.$delims[1])
                        ->raw(' => abs(')
                        ->subcompile($this->hasNode('count') ? $this->getNode('count') : null)
                        ->raw('), ')
                    ;
                } else {
                    $compiler
                        ->string($delims[0].$name.$delims[1])
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

            if ($this->hasNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->hasNode('count') ? $this->getNode('count') : null)
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
            $delims = $this->options->getDelimiters();
            $complex = $this->options->getComplexVars();
            $msg = '';

            foreach ($body as $node) {
                if (get_class($node) === 'Twig_Node' && $node->getNode(0) instanceof Twig_Node_SetTemp) {
                    $node = $node->getNode(1);
                }

                if ($node instanceof Twig_Node_Print) {
                    if ($complex) {
                        $expr = $node->getNode('expr');
                        $name = $this->guessNameFromExpression($expr);
                        $unique = $this->makeUnique($vars, $name, $expr);

                        $msg .= sprintf('%s%s%s', $delims[0], $unique, $delims[1]);
                        $vars[$unique] = $expr;
                    } else {
                        $n = $node->getNode('expr');
                        while ($n instanceof Twig_Node_Expression_Filter) {
                            $n = $n->getNode('node');
                        }
                        $msg .= sprintf('%s%s%s', $delims[0], $n->getAttribute('name'), $delims[1]);
                        $vars[] = new Twig_Node_Expression_Name($n->getAttribute('name'), $n->getTemplateLine());
                    }
                } else {
                    $msg .= $node->getAttribute('data');
                }
            }
        } else {
            $msg = $body->getAttribute('data');
        }

        return array(new Twig_Node(array(new Twig_Node_Expression_Constant($this->options->getNormalize() ? $this->normalizeString($msg) : trim($msg), $body->getTemplateLine()))), $vars);
    }

    /**
     * Normalizes a string (removes spaces inside the string).
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
     * @param string $msg  The message string to be normalized.
     * @param string $glue The character used to replace spaces.
     *
     * @return string The normalized string.
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
     *
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
     *
     * @return array Array of names guessed up to this node.
     *
     * @throws Twig_Error_Syntax
     */
    protected function extractNames(\Twig_Node $node)
    {
        if ($node instanceof Expression\GetAttrExpression) {
            return array_merge(
                $this->extractNames($node->getNode('node')),
                $this->extractNames($node->getNode('attribute'))
            );
        }

        if (($node instanceof Expression\NameExpression) || ($node instanceof Expression\TempNameExpression)) {
            return array($node->getAttribute('name'));
        }

        if ($node instanceof Expression\ConstantExpression) {
            // Constants may have spaces in it. Normalize it!
            return array($this->normalizeString($node->getAttribute('value'), '_'));
        }

        throw new Twig_Error_Syntax('Sorry, the expression is too complex to use as "trans" variable as is. Please consider using an "as" filter.', $node->getTemplateLine());
    }

    /**
     * Makes a variable name unique by adding a serial number if the variable
     * name already exists and its expressions are different. In other words,
     * we only add serial numbers to variables who uses different filters, etc.
     *
     * @param array    $vars The existing variables array
     * @param string   $name The proposed new name
     * @param TwigNode $expr The expression for this variable
     *
     * @return string The new unique name
     */
    protected function makeUnique(array $vars, $name, \Twig_Node $expr)
    {
        // Loop through until we get a free name. Note that the starting index
        // is "2" instead of "1". This gets us a good looking name series like
        // "name", "name_2",... It would be ugly to have "name" and "name_1"
        $index = 2;
        $new_name = $name;
        while (array_key_exists($new_name, $vars) && (!$this->isNodeSimilar($vars[$new_name], $expr))) {
            $new_name = $name.'_'.$index++;
        }

        return $new_name;
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

    /**
     * Checks that two nodes are similar. This includes deep comparing their
     * child nodes. Two nodes are similar if they both share the same tags,
     * attributes and their child nodes are also similar.
     *
     * This explicity leaves out the "lineno" attribute and the "specialVars"
     * who in the context of the {%trans%} tag they must be equal.
     *
     * @param TwigNode $first  The first node, source of the comparison
     * @param TwigNode $second The second node to compare against
     *
     * @return bool Returns True if both Nodes seems similar
     */
    protected function isNodeSimilar(\Twig_Node $first, \Twig_Node $second)
    {
        // First sign that the Nodes are not even similar. Fail fast...
        if (($first->tag != $second->tag) || ($first->attributes != $second->attributes) || ($first->count() != $second->count())) {
            return false;
        }

        // Iterate each node for similarity
        foreach ($first->nodes as $key => $value) {
            if (!$second->hasNode($key)) {
                return false;
            }
            if (!$this->isNodeSimilar($value, $second->getNode($key))) {
                return false;
            }
        }

        // Ok, seems similar...
        return true;
    }

}

class_alias('Twig_Extensions_Node_Trans', 'Twig\Extensions\Node\TransNode', false);
