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
 * Represents a cache node.
 *
 * @package    twig
 * @subpackage Twig-extensions
 * @author     Anler Hernandez Peral <anler86@gmail.com>
 * @version    SVN: $Id$
 */
class Twig_Extensions_Node_Cache extends Twig_Node
{
    public function __construct($key, Twig_NodeInterface $value, $time, $dynamicKey = false, $lineno, $tag = 'cache')
    {
        // $dynamicKey: If true then compute the keyname from typecasting to
        // a string the object referenced by $key that's in the context
        parent::__construct(array('value' => $value), array('key' => $key, 'time' => $time, 'dynamicKey' => $dynamicKey), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $key = $this->getAttribute('key');
        $dynamicKey = $this->getAttribute('dynamicKey');
        $time = $this->getAttribute('time');

        $subcompiler = new Twig_Compiler($compiler->getEnvironment());
        $subcompiler
            ->addDebugInfo($this)
            ->subcompile($this->getNode('value'));
        $source = $subcompiler->getSource();
        $compiler
            ->addDebugInfo($this)
            ->write("\$twigExtensionCacheBackend = \$this->getEnvironment()->getExtension('{$this->getNodeTag()}')->getCacheBackend();\n")
            ->write(
                $dynamicKey ?
                "\$twigExtensionCacheValue = \$twigExtensionCacheBackend->get((string)\$context['$key']);\n" :
                "\$twigExtensionCacheValue = \$twigExtensionCacheBackend->get('$key');\n"
            )
            ->write("if (\$twigExtensionCacheValue === null) {\n")
            ->indent()
            ->write("ob_start();\n")
            ->write("$source")
            ->write("\$twigExtensionCacheValue = ob_get_clean();\n")
            ->write("\$twigExtensionCacheBackend->set('$key', \$twigExtensionCacheValue, $time);\n")
            ->outdent()
            ->write("}\n")
            ->write("echo \$twigExtensionCacheValue;\n");
    }
}
