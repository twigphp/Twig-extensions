
<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Thomas Bretzke <tb@itembase.biz>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_Money extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array(
            'money_format' => new Twig_Filter_Function('twig_money_format_filter'),
        );

        return $filters;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'Money';
    }
}

function twig_money_format_filter($number, $locale, $format)
{
    setlocale(LC_MONETARY, $locale);
    return money_format($format, $number);
}
