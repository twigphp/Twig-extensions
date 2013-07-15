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
        $condition = new Twig_Node_Expression_Constant(true, 1);
        $body      = new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 1), 1);
        $node      = new Twig_Extensions_Node_While($condition, $body, 1);

        $this->assertEquals($condition, $node->getNode('condition'));
        $this->assertEquals($body, $node->getNode('body'));
    }

    /**
     * @covers Twig_Node_For::compile
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null)
    {
        parent::testCompile($node, $source, $environment);
    }

    /**
     * Test data
     *
     * @return array(array(Twig_Node, string))
     */
    public function getTests()
    {
        $tests = array();

        $condition = new Twig_Node_Expression_Constant(true, 1);
        $body      = new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 1), 1);
        $node      = new Twig_Extensions_Node_While($condition, $body, 1);

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
