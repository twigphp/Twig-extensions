<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Arjen Brouwer <info@arjenbrouwer.nl>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_If extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'if' => new Twig_Filter_Function('twig_if_filter')
        );
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'if';
    }
}

function twig_if_filter($value, $condition)
{
    return ($condition) ? $value : null;
}
