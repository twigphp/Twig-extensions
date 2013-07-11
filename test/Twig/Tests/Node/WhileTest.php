<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Extensions_Node_WhileTest extends Twig_Test_NodeTestCase
{
    /**
     * @covers WhileNode::__construct
     */
    public function testConstructor()
    {
        $t    = new Twig_Node(array(
            new Twig_Node_Expression_Constant(true, 1),
            new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 1), 1),
        ), array(), 1);
        $else = null;
        $node = new Twig_Extensions_Node_While($t, 1);

        $this->assertEquals($t, $node->getNode('tests'));
    }

    /**
     * Test data
     *
     * @return array(array(Twig_Node, string))
     */
    public function getTests()
    {
        $tests = array();

        $t    = new Twig_Node(array(
            new Twig_Node_Expression_Constant(true, 1),
            new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 1), 1),
        ), array(), 1);
        $node = new Twig_Extensions_Node_While($t, 1);

        $tests[] = array($node, <<<EOF
// line 1
while (true) {
    echo {$this->getVariableGetter('foo')};
}
EOF
        );

        return $tests;
    }
}
