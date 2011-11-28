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
        return array('number' => new Twig_Filter_Function('number_format'),
                     'money'  => new Twig_Filter_Function('twig_money_format'),
                     
        );
    }
    /**
     * Returns a list of funtions.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array('rand'   => new Twig_Function_Function('rand'),
                     
        );
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

/**
 * A wrapper had to be defined due to php inconsistent API
*/
function twig_money_format($number, $format)
{
    return money_format($format, $number);
}
