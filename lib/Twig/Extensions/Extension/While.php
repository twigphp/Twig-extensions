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
 * While loop extension
 *
 * @package    Twig
 * @subpackage Twig-extensions
 * @author     Alexey Buyanow <alexbuyanow@gmail.com>
 */
class Twig_Extensions_Extension_While extends Twig_Extension
{
    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
            new Twig_Extensions_TokenParser_While(),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'while';
    }
}
