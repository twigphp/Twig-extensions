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
    function twig_format_currency($value, $currency, $locale = null)
    {
        static $formatter;

        if (null === $locale) {
            $locale = Locale::getDefault();
        }

        if (null === $formatter) {
            $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        }

        return $formatter->formatCurrency($value, $currency);
    }
} else {
    function twig_format_currency($value, $currency = null, $locale = null)
    {
        if (null !== $currency) {
            throw new \LogicException("You must have Intl enabled to specify a currency. Pass null if you\'re trying to set the locale.");
        }

        if (null !== $locale) {
            setlocale(LC_MONETARY, $locale);
        }
        
        return money_format("%i", $value);
    }
}