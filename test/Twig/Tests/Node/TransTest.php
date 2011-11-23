<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once TWIG_LIB_DIR.'/../test/Twig/Tests/Node/TestCase.php';

class Twig_Tests_Node_TransTest extends Twig_Tests_Node_TestCase
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
        $node = new Twig_Extensions_Node_Trans($body, $plural, $count, 0);

        $this->assertEquals($body, $node->getNode('body'));
        $this->assertEquals($count, $node->getNode('count'));
        $this->assertEquals($plural, $node->getNode('plural'));
    }

    public function getTests()
    {
        $tests = array();

        $body = new Twig_Node_Expression_Name('foo', 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, 0);
        $tests[] = array($node, sprintf('echo gettext(%s);', $this->getVariableGetter('foo')));

        $body = new Twig_Node_Expression_Constant('Hello', 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, 0);
        $tests[] = array($node, 'echo gettext("Hello");');

        $body = new Twig_Node(array(
            new Twig_Node_Text('Hello', 0),
        ), array(), 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, 0);
        $tests[] = array($node, 'echo gettext("Hello");');

        $body = new Twig_Node(array(
            new Twig_Node_Text('J\'ai ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 0), 0),
            new Twig_Node_Text(' pommes', 0),
        ), array(), 0);
        $node = new Twig_Extensions_Node_Trans($body, null, null, 0);
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
        $node = new Twig_Extensions_Node_Trans($body, $plural, $count, 0);
        $tests[] = array($node, sprintf('echo strtr(ngettext("Hey %%name%%, I have one apple", "Hey %%name%%, I have %%count%% apples", abs(12)), array("%%name%%" => %s, "%%name%%" => %s, "%%count%%" => abs(12), ));', $this->getVariableGetter('name'), $this->getVariableGetter('name')));

        // with escaper extension set to on
        $body = new Twig_Node(array(
            new Twig_Node_Text('J\'ai ', 0),
            new Twig_Node_Print(new Twig_Node_Expression_Filter(new Twig_Node_Expression_Name('foo', 0), new Twig_Node_Expression_Constant('escape', 0), new Twig_Node(), 0), 0),
            new Twig_Node_Text(' pommes', 0),
        ), array(), 0);

        $node = new Twig_Extensions_Node_Trans($body, null, null, 0);
        $tests[] = array($node, sprintf('echo strtr(gettext("J\'ai %%foo%% pommes"), array("%%foo%%" => %s, ));', $this->getVariableGetter('foo')));

        return $tests;
    }
}
