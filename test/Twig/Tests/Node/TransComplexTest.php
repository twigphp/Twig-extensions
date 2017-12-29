<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Node_TransComplexTest extends Twig_Test_NodeTestCase
{
    static $environment = null;

    const DEBUG_LINE = "// line 1\n";

    public function getEnvironment()
    {
        return static::$environment;
    }

    public function getTests()
    {
        // Somehow, "getTests" gets called by PHPUnit before "setUpBeforeClass"
        // so we have to initialize a common Environment here instead to be
        // able to use the "parseTrans" method.
        static::$environment = new Twig_Environment(new Twig_Loader_Array(array()), array(
            'autoescape' => false,
            'optimizations' => Twig_NodeVisitor_Optimizer::OPTIMIZE_NONE,
        ));
        static::$environment->addExtension(new Twig_Extensions_Extension_I18n(array(
            'complex_vars' => true,
        )));

        $tests = array();

        // Testing simple "complex" variables.
        $node = $this->parseTrans('{% trans %}J\'ai {{user.pommes}}{% endtrans %}');
        $tests[] = array($node, self::DEBUG_LINE.'echo strtr(gettext("J\'ai %user_pommes%"), array("%user_pommes%" => twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "pommes", array()), ));');

        // Testing somewhat more complex "complex" variables.
        $node = $this->parseTrans('{% trans %}J\'ai {{user["first"].pommes}}{% endtrans %}');
        $tests[] = array($node, self::DEBUG_LINE.'echo strtr(gettext("J\'ai %user_first_pommes%"), array("%user_first_pommes%" => twig_get_attribute($this->env, $this->getSourceContext(), twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "first", array(), "array"), "pommes", array()), ));');

        // Testing multiple complex fields gets same name.
        $node = $this->parseTrans('{% trans %}Je suis {{user.name}} {{user.name}}{% endtrans %}');
        $tests[] = array($node, self::DEBUG_LINE.'echo strtr(gettext("Je suis %user_name% %user_name%"), array("%user_name%" => twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "name", array()), ));');

        // Testing multiple complex fields gets different names.
        $node = $this->parseTrans('{% trans %}Je suis {{user.name | upper}} {{user.name | lower}}{% endtrans %}');
        $tests[] = array($node, self::DEBUG_LINE.'echo strtr(gettext("Je suis %user_name% %user_name_2%"), array("%user_name%" => twig_upper_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "name", array())), "%user_name_2%" => twig_lower_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "name", array())), ));');

        // Testing plurals still works using complex algorithm.
        // NOTE: Notice the "\n" line breaks so that {{count}} variables are on different lines
        $node = $this->parseTrans("{% trans %}\nI have {{count}} apple\n{% plural 12 %}\nI have {{count}} apples\n{% endtrans %}");
        $tests[] = array($node, self::DEBUG_LINE.'echo strtr(ngettext("I have %count% apple", "I have %count% apples", abs(12)), array("%count%" => abs(12), ));');

        // Testing plurals gets the same complex variable name and value.
        // NOTE: Notice the "\n" line breaks so that {{count}} variables are on different lines
        $node = $this->parseTrans("{% trans %}\nHi {{user.name}}, i have {{count}} apple\n{% plural 12 %}\nHi {{user.name}} I have {{count}} apples\n{% endtrans %}");
        $tests[] = array($node, self::DEBUG_LINE.'echo strtr(ngettext("Hi %user_name%, i have %count% apple", "Hi %user_name% I have %count% apples", abs(12)), array("%user_name%" => twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user', 4).', "name", array()), "%count%" => abs(12), ));');

        // Testing plurals gets different complex variable names and values.
        $node = $this->parseTrans('{% trans %}Sorry {{user.name}}, i only have one apple{% plural 12 %}YUP! {{user.name | upper}} I HAVE {{count}} APPLES{% endtrans %}');
        $tests[] = array($node, self::DEBUG_LINE.'echo strtr(ngettext("Sorry %user_name%, i only have one apple", "YUP! %user_name_2% I HAVE %count% APPLES", abs(12)), array("%user_name%" => twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "name", array()), "%user_name_2%" => twig_upper_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "name", array())), "%count%" => abs(12), ));');

        // Testing plurals with different complex variable names and values works even with "as" filter.
        $node = $this->parseTrans('{% trans %}Sorry {{user.name | as("name")}}, i only have one apple{% plural 12 %}YUP! {{user.name | upper | as("name")}} I HAVE {{count}} APPLES{% endtrans %}');
        $tests[] = array($node, self::DEBUG_LINE.'echo strtr(ngettext("Sorry %name%, i only have one apple", "YUP! %name_2% I HAVE %count% APPLES", abs(12)), array("%name%" => twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "name", array()), "%name_2%" => twig_upper_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), '.$this->getVariableGetter('user').', "name", array())), "%count%" => abs(12), ));');

        return $tests;
    }

    /**
     * Parses a Twig source. Returns the parsed Trans Node tree so that we
     * don't have to recreate it manually using new Twig_Node_Trans(...), etc.
     *
     * @param string $source The template source.
     *
     * @return Twig_Node_Module The parsed template.
     */
    private function parseTrans($source)
    {
        $lexer = new Twig_Lexer(static::$environment);
        $parser = new Twig_Parser(static::$environment);
        $module = $parser->parse($lexer->tokenize(new Twig_Source($source, uniqid())));

        // The Twig_Extensions_Node_Trans we look for is the first (and only)
        // node of the Twig_Node_Body. If it's not, some error will be thrown
        // along the test chain. :-)
        return $module->getNode('body')->getNode(0);
    }

}
