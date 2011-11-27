<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author  Jonathan N <jnonon@gmail.com>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_Number extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array('number' => new Twig_Filter_Function('number_format'));
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'Number';
    }
}
