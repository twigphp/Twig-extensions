<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Tim Nagel <tim@nagel.com.au>
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
        return array(
            'currency' => new Twig_Filter_Function('twig_format_currency'),
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

if (class_exists('NumberFormatter')) {
    function twig_format_currency($value, $currency)
    {
        static $formatter;
        if (null === $formatter) {
            $formatter = new NumberFormatter(Locale::getDefault(), NumberFormatter::CURRENCY);
        }

        return $formatter->formatCurrency($value, $currency);
    }
} else {
    function twig_format_currency($value)
    {
        return money_format("%i", $value);
    }
}