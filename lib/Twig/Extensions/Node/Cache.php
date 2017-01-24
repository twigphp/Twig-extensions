<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a cache node.
 *
 * @package    twig
 * @subpackage Twig-extensions
 * @author     Anler Hernandez Peral <anler86@gmail.com>
 */
class Twig_Extensions_Node_Cache extends Twig_Node
{
    public function __construct(Twig_NodeInterface $key, Twig_NodeInterface $value, $time, $lineno, $tag = 'cache')
    {
        parent::__construct(array('key' => $key, 'value' => $value), array('time' => $time), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $time = $this->getAttribute('time');

        $compiler
            ->addDebugInfo($this)
            ->write("\$twigExtensionCacheBackend = \$this->getEnvironment()->getExtension('{$this->getNodeTag()}')->getCacheBackend();\n")
            ->write("\$twigExtensionCacheKey = ")
            ->subcompile($this->getNode('key'))
            ->write(";\n")
            ->write("\$twigExtensionCacheValue = \$twigExtensionCacheBackend->get(\$twigExtensionCacheKey);\n")
            ->write("if (null === \$twigExtensionCacheValue) {\n")
            ->indent()
            ->write("ob_start();\n")
            ->subcompile($this->getNode('value'))
            ->write("\$twigExtensionCacheValue = ob_get_clean();\n")
            ->write("\$twigExtensionCacheBackend->set(\$twigExtensionCacheKey, \$twigExtensionCacheValue, $time);\n")
            ->outdent()
            ->write("}\n")
            ->write("echo \$twigExtensionCacheValue;\n");
    }
}
