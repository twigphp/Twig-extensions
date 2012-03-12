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

        $key = 'keyname';
        $time = 100;
		$value = new Twig_Node(array(new Twig_Node_Text($staticContent, 0)));
		$node = new Twig_Extensions_Node_Cache($key, $value, $time, 0);

        $this->assertEquals($value, $node->getNode('value'));
        $this->assertEquals($key, $node->getAttribute('key'));
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

		$key = 'keyname';
		$value = new Twig_Node(array(new Twig_Node_Text($staticContent, 0)));
		$textnode = new Twig_Extensions_Node_Cache($key, $value, $time, 0);

		$t = new Twig_Node(array(
			new Twig_Node_Expression_Constant(true, 0),
			new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 0), 0)
		), array(), 0);
		$else = null;
		$value = new Twig_Node_If($t, $else, 0);
		$ifnode = new Twig_Extensions_Node_Cache($key, $value, $time, 0);

		return array(
			array($textnode, <<<EOF
\$twigExtensionCache = Twig_Extensions_Extension_Cache_CacheHandler::getInstance()->getCacheBackend();
\$twigExtensionCacheValue = \$twigExtensionCache->get('$key');
if (\$twigExtensionCacheValue === null) {
    ob_start();
    echo "$staticContent";
    \$twigExtensionCacheValue = ob_get_clean();
    \$twigExtensionCache->set('$key', \$twigExtensionCacheValue, 100);
}
echo \$twigExtensionCacheValue;
EOF
			),
			array($ifnode, <<<EOF
\$twigExtensionCache = Twig_Extensions_Extension_Cache_CacheHandler::getInstance()->getCacheBackend();
\$twigExtensionCacheValue = \$twigExtensionCache->get('$key');
if (\$twigExtensionCacheValue === null) {
    ob_start();
    if (true) {
    echo {$this->getVariableGetter('foo')};
}
    \$twigExtensionCacheValue = ob_get_clean();
    \$twigExtensionCache->set('$key', \$twigExtensionCacheValue, 100);
}
echo \$twigExtensionCacheValue;
EOF
			)
		);
    }
}
