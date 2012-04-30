<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Extensions_Extension_Intl extends Twig_Extension
{
    public function __construct()
    {
        if (!class_exists('IntlDateFormatter') || !class_exists('NumberFormatter')) {
            throw new RuntimeException('The intl extension is needed to use intl-based filters.');
        }
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('localizeddate', 'twig_localized_date_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('localizednumber', 'twig_localized_number_filter'),
            new Twig_SimpleFilter('localizedcurrency', 'twig_localized_currency_filter'),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'intl';
    }
}

function twig_localized_date_filter(Twig_Environment $env, $date, $dateFormat = 'medium', $timeFormat = 'medium', $locale = null, $timezone = null, $format = null)
{
    $date = twig_date_converter($env, $date, $timezone);

    $formatValues = array(
        'none'   => IntlDateFormatter::NONE,
        'short'  => IntlDateFormatter::SHORT,
        'medium' => IntlDateFormatter::MEDIUM,
        'long'   => IntlDateFormatter::LONG,
        'full'   => IntlDateFormatter::FULL,
    );

    $formatter = IntlDateFormatter::create(
        $locale,
        $formatValues[$dateFormat],
        $formatValues[$timeFormat],
        $date->getTimezone()->getName(),
        IntlDateFormatter::GREGORIAN,
        $format
    );

    return $formatter->format($date->getTimestamp());
}

function twig_localized_number_filter($number, $style = 'decimal', $format = 'default', $currency = null, $locale = null )
{
    $formatter = getNumberFormatter(
        $locale !== null ? $locale : Locale::getDefault(),
        $style
    );

    $formatValues = array(
        'default'   => NumberFormatter::TYPE_DEFAULT,
        'int32'     => NumberFormatter::TYPE_INT32,
        'int64'     => NumberFormatter::TYPE_INT64,
        'double'    => NumberFormatter::TYPE_DOUBLE,
        'currency'  => NumberFormatter::TYPE_CURRENCY,
    );

    return $formatter->format(
        $number,
        $formatValues[$format]);
}

function twig_localized_currency_filter($number, $currency = null, $locale = null)
{
    $formatter = getNumberFormatter(
        $locale !== null ? $locale : Locale::getDefault(),
        'currency'
    );

    return $formatter->formatCurrency($number, $currency);
}

function getNumberFormatter($locale, $style)
{
    $styleValues = array(
        'decimal'       => NumberFormatter::DECIMAL,
        'currency'      => NumberFormatter::CURRENCY,
        'percent'       => NumberFormatter::PERCENT,
        'scientific'    => NumberFormatter::SCIENTIFIC,
        'spellout'      => NumberFormatter::SPELLOUT,
        'ordinal'       => NumberFormatter::ORDINAL,
        'duration'      => NumberFormatter::DURATION,
    );

    return NumberFormatter::create(
        $locale !== null ? $locale : Locale::getDefault(),
        $styleValues[$style]
    );
}
