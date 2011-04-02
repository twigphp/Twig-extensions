<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Francisco Ancona Lopes <chico.lopes@gmail.com>
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
        return array('number' => new Twig_Filter_Function('twig_number_filter'));
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

function twig_number_filter($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
{
    return number_format($number, $decimals, $dec_point, $thousands_sep);
}
