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

class Twig_Tests_Node_CacheTest extends Twig_Tests_Node_TestCase
{
    /**
     * @covers Twig_Node_Cache::__construct
     */
    public function testConstructor()
    {
        $staticContent = '<div>static content</div>';

        $key = new Twig_Node_Text('keyname', 0);
        $dynamicKey = false;
        $time = 100;
        $value = new Twig_Node(array(new Twig_Node_Text($staticContent, 0)));
        $node = new Twig_Extensions_Node_Cache($key, $value, $time, $dynamicKey, 0);

        $this->assertEquals($value, $node->getNode('value'));
        $this->assertEquals($key, $node->getNode('key'));
    }

    /**
     * @covers Twig_Node_Cache::compile
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null)
    {
        parent::testCompile($node, $source, $environment);
    }

    public function getTests()
    {
        $time = 100; // 100 seconds
        $staticContent = '<div>static content</div>';

        $key = new Twig_Node_Expression_Constant('keyname', 0);
        $value = new Twig_Node(array(new Twig_Node_Text($staticContent, 0)));
        $textnode = new Twig_Extensions_Node_Cache($key, $value, $time, 0);

        $t = new Twig_Node(array(
            new Twig_Node_Expression_Constant(true, 0),
            new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 0), 0)
        ), array(), 0);
        $else = null;
        $value = new Twig_Node_If($t, $else, 0);
        $ifnode = new Twig_Extensions_Node_Cache($key, $value, $time, 0);

        $value = new Twig_Node(array(new Twig_Node_Text($staticContent, 0)));
        $dynamicnode = new Twig_Extensions_Node_Cache($key, $value, $time, 0);

        return array(
            array($textnode, <<<EOF
\$twigExtensionCacheBackend = \$this->getEnvironment()->getExtension('cache')->getCacheBackend();
\$twigExtensionCacheKey = "keyname";
\$twigExtensionCacheValue = \$twigExtensionCacheBackend->get(\$twigExtensionCacheKey);
if (null === \$twigExtensionCacheValue) {
    ob_start();
    echo "$staticContent";
    \$twigExtensionCacheValue = ob_get_clean();
    \$twigExtensionCacheBackend->set(\$twigExtensionCacheKey, \$twigExtensionCacheValue, 100);
}
echo \$twigExtensionCacheValue;
EOF
            ),
            array($ifnode, <<<EOF
\$twigExtensionCacheBackend = \$this->getEnvironment()->getExtension('cache')->getCacheBackend();
\$twigExtensionCacheKey = "keyname";
\$twigExtensionCacheValue = \$twigExtensionCacheBackend->get(\$twigExtensionCacheKey);
if (null === \$twigExtensionCacheValue) {
    ob_start();
    if (true) {
        echo {$this->getVariableGetter('foo')};
    }
    \$twigExtensionCacheValue = ob_get_clean();
    \$twigExtensionCacheBackend->set(\$twigExtensionCacheKey, \$twigExtensionCacheValue, 100);
}
echo \$twigExtensionCacheValue;
EOF
            ),
            array($dynamicnode, <<<EOF
\$twigExtensionCacheBackend = \$this->getEnvironment()->getExtension('cache')->getCacheBackend();
\$twigExtensionCacheKey = "keyname";
\$twigExtensionCacheValue = \$twigExtensionCacheBackend->get(\$twigExtensionCacheKey);
if (null === \$twigExtensionCacheValue) {
    ob_start();
    echo "$staticContent";
    \$twigExtensionCacheValue = ob_get_clean();
    \$twigExtensionCacheBackend->set(\$twigExtensionCacheKey, \$twigExtensionCacheValue, 100);
}
echo \$twigExtensionCacheValue;
EOF
            ),
        );
    }
}
