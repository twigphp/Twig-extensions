<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Node_TransTest extends Twig_Test_NodeTestCase
{
    /**
     * @covers Twig_Node_Trans::__construct
     */
    public function testConstructor()
    {
        $count = new Twig_Node_Expression_Constant(12, 0);
        $body = new Twig_Node(array(
            new Twig_Node_Text('Hello', 0),
        ), array(), 0);
        $plural = new Twig_Node(array(
            new Twig_Node_Text('Hey ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('name', 0), 0),
            new Twig_Node_Text(', I have ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('count', 0), 0),
            new Twig_Node_Text(' apples', 0),
        ), array(), 0);
        $node = new Twig_Extensions_Node_Trans($body, $plural, $count, null,null, 0);

        $this->assertEquals($body, $node->getNode('body'));
        $this->assertEquals($count, $node->getNode('count'));
        $this->assertEquals($plural, $node->getNode('plural'));
    }

    public function getTests()
    {
        $tests = array();

        $body = new Twig_Node_Expression_Name('foo', 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, null, null, 0);
        $tests[] = array($node, sprintf('echo gettext(%s);', $this->getVariableGetter('foo')));

        $body = new Twig_Node_Expression_Constant('Hello', 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, null, null, 0);
        $tests[] = array($node, 'echo gettext("Hello");');

        $body = new Twig_Node(array(
            new Twig_Node_Text('Hello', 0),
        ), array(), 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, null, null, 0);
        $tests[] = array($node, 'echo gettext("Hello");');

        $body = new Twig_Node(array(
            new Twig_Node_Text('J\'ai ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 0), 0),
            new Twig_Node_Text(' pommes', 0),
        ), array(), 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, null, null, 0);
        $tests[] = array($node, sprintf('echo strtr(gettext("J\'ai %%foo%% pommes"), array("%%foo%%" => %s, ));', $this->getVariableGetter('foo')));

        $count = new Twig_Node_Expression_Constant(12, 0);
        $body = new Twig_Node(array(
            new Twig_Node_Text('Hey ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('name', 0), 0),
            new Twig_Node_Text(', I have one apple', 0),
        ), array(), 0);
        $plural = new Twig_Node(array(
            new Twig_Node_Text('Hey ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('name', 0), 0),
            new Twig_Node_Text(', I have ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('count', 0), 0),
            new Twig_Node_Text(' apples', 0),
        ), array(), 0);
        $node = new Twig_Extensions_Node_Trans($body, $plural, $count, null, null, 0);
        $tests[] = array($node, sprintf('echo strtr(ngettext("Hey %%name%%, I have one apple", "Hey %%name%%, I have %%count%% apples", abs(12)), array("%%name%%" => %s, "%%name%%" => %s, "%%count%%" => abs(12), ));', $this->getVariableGetter('name'), $this->getVariableGetter('name')));

        // with escaper extension set to on
        $body = new Twig_Node(array(
            new Twig_Node_Text('J\'ai ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Filter(new Twig_Node_Expression_Name('foo', 0), new Twig_Node_Expression_Constant('escape', 0), new Twig_Node(), 0), 0),
            new Twig_Node_Text(' pommes', 0),
        ), array(), 0);

        $node = new Twig_Extensions_Node_Trans($body, null, null, null, null, 0);
        $tests[] = array($node, sprintf('echo strtr(gettext("J\'ai %%foo%% pommes"), array("%%foo%%" => %s, ));', $this->getVariableGetter('foo')));

        // with notes
        $body = new Twig_Node_Expression_Constant('Hello', 0);
        $notes = new Twig_Node_Text('Notes for translators', 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, $notes, null, 0);
        $tests[] = array($node, "// notes: Notes for translators\necho gettext(\"Hello\");");

        $body = new Twig_Node_Expression_Constant('Hello', 0);
        $notes = new Twig_Node_Text("Notes for translators\nand line breaks", 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, $notes, null, 0);
        $tests[] = array($node, "// notes: Notes for translators and line breaks\necho gettext(\"Hello\");");

        $count = new Twig_Node_Expression_Constant(5, 0);
        $body = new Twig_Node_Text('There is 1 pending task', 0);
        $plural = new Twig_Node(array(
            new Twig_Node_Text('There are ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('count', 0), 0),
            new Twig_Node_Text(' pending tasks', 0),
        ), array(), 0);
        $notes = new Twig_Node_Text('Notes for translators', 0);
        $node = new Twig_Extensions_Node_Trans($body, $plural, $count, $notes, null, 0);
        $tests[] = array($node, "// notes: Notes for translators\n".'echo strtr(ngettext("There is 1 pending task", "There are %count% pending tasks", abs(5)), array("%count%" => abs(5), ));');

        //with context
        $body = new Twig_Node_Expression_Constant('Hello', 0);
        $context = new Twig_Node_Text('Sidebar|Menu', 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, null, $context, 0);
        $tests[] = array($node, 'echo twig_pgettext("Hello", "Sidebar|Menu");');

        $count = new Twig_Node_Expression_Constant(5, 0);
        $body = new Twig_Node_Text('There is 1 pending task', 0);
        $plural = new Twig_Node(array(
            new Twig_Node_Text('There are ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('count', 0), 0),
            new Twig_Node_Text(' pending tasks', 0),
        ), array(), 0);
        $context = new Twig_Node_Text('Sidebar|Menu', 0);
        $node = new Twig_Extensions_Node_Trans($body, $plural, $count, null, $context, 0);
        $tests[] = array($node, 'echo strtr(twig_npgettext("There is 1 pending task", "There are %count% pending tasks", abs(5), "Sidebar|Menu"), array("%count%" => abs(5), ));');

        $body = new Twig_Node_Expression_Constant('Hello', 0);
        $notes = new Twig_Node_Text("Notes for translators\nand line breaks", 0);
        $context = new Twig_Node_Text('Sidebar|Menu', 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, $notes, $context, 0);
        $tests[] = array($node, "// notes: Notes for translators and line breaks\necho twig_pgettext(\"Hello\", \"Sidebar|Menu\");");

        $count = new Twig_Node_Expression_Constant(5, 0);
        $body = new Twig_Node_Text('There is 1 pending task', 0);
        $plural = new Twig_Node(array(
            new Twig_Node_Text('There are ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('count', 0), 0),
            new Twig_Node_Text(' pending tasks', 0),
        ), array(), 0);
        $context = new Twig_Node_Text('Sidebar|Menu', 0);
        $notes = new Twig_Node_Text('Notes for translators', 0);
        $node = new Twig_Extensions_Node_Trans($body, $plural, $count, $notes, $context, 0);
        $tests[] = array($node, "// notes: Notes for translators\n".'echo strtr(twig_npgettext("There is 1 pending task", "There are %count% pending tasks", abs(5), "Sidebar|Menu"), array("%count%" => abs(5), ));');

        return $tests;
    }
}
