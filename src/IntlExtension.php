<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010-2019 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Extensions
{
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFilter;

    class IntlExtension extends AbstractExtension
    {
        public function __construct()
        {
            if (!class_exists(\IntlDateFormatter::class)) {
                throw new RuntimeException('The native PHP intl extension (http://php.net/manual/en/book.intl.php) is needed to use intl-based filters.');
            }
        }

        public function getFilters()
        {
            return [
                new TwigFilter('localizeddate', 'twig_localized_date_filter', ['needs_environment' => true]),
                new TwigFilter('localizednumber', 'twig_localized_number_filter'),
                new TwigFilter('localizedcurrency', 'twig_localized_currency_filter'),
            ];
        }
    }
}

namespace
{
    use Twig\Environment;
    use Twig\Error\SyntaxError;

    function twig_localized_date_filter(Environment $env, $date, $dateFormat = 'medium', $timeFormat = 'medium', $locale = null, $timezone = null, $format = null, $calendar = 'gregorian')
    {
        $date = \twig_date_converter($env, $date, $timezone);

        $formatValues = [
            'none' => \IntlDateFormatter::NONE,
            'short' => \IntlDateFormatter::SHORT,
            'medium' => \IntlDateFormatter::MEDIUM,
            'long' => \IntlDateFormatter::LONG,
            'full' => \IntlDateFormatter::FULL,
        ];

        if (!class_exists('IntlTimeZone')) {
            $formatter = \IntlDateFormatter::create(
                $locale,
                $formatValues[$dateFormat],
                $formatValues[$timeFormat],
                $date->getTimezone()->getName(),
                'gregorian' === $calendar ? \IntlDateFormatter::GREGORIAN : \IntlDateFormatter::TRADITIONAL,
                $format
            );

            return $formatter->format($date->getTimestamp());
        }

        $formatter = \IntlDateFormatter::create(
            $locale,
            $formatValues[$dateFormat],
            $formatValues[$timeFormat],
            \IntlTimeZone::createTimeZone($date->getTimezone()->getName()),
            'gregorian' === $calendar ? \IntlDateFormatter::GREGORIAN : \IntlDateFormatter::TRADITIONAL,
            $format
        );

        return $formatter->format($date->getTimestamp());
    }

    function twig_localized_number_filter($number, $style = 'decimal', $type = 'default', $locale = null)
    {
        static $typeValues = [
            'default' => \NumberFormatter::TYPE_DEFAULT,
            'int32' => \NumberFormatter::TYPE_INT32,
            'int64' => \NumberFormatter::TYPE_INT64,
            'double' => \NumberFormatter::TYPE_DOUBLE,
            'currency' => \NumberFormatter::TYPE_CURRENCY,
        ];

        $formatter = twig_get_number_formatter($locale, $style);

        if (!isset($typeValues[$type])) {
            throw new SyntaxError(sprintf('The type "%s" does not exist. Known types are: "%s"', $type, implode('", "', array_keys($typeValues))));
        }

        return $formatter->format($number, $typeValues[$type]);
    }

    function twig_localized_currency_filter($number, $currency = null, $locale = null)
    {
        $formatter = twig_get_number_formatter($locale, 'currency');

        return $formatter->formatCurrency($number, $currency);
    }

    /**
     * Gets a number formatter instance according to given locale and formatter.
     *
     * @param string $locale Locale in which the number would be formatted
     * @param int    $style  Style of the formatting
     *
     * @return \NumberFormatter A NumberFormatter instance
     */
    function twig_get_number_formatter($locale, $style)
    {
        static $formatter, $currentStyle;

        $locale = null !== $locale ? $locale : \Locale::getDefault();

        if ($formatter && $formatter->getLocale() === $locale && $currentStyle === $style) {
            // Return same instance of NumberFormatter if parameters are the same
            // to those in previous call
            return $formatter;
        }

        static $styleValues = [
            'decimal' => \NumberFormatter::DECIMAL,
            'currency' => \NumberFormatter::CURRENCY,
            'percent' => \NumberFormatter::PERCENT,
            'scientific' => \NumberFormatter::SCIENTIFIC,
            'spellout' => \NumberFormatter::SPELLOUT,
            'ordinal' => \NumberFormatter::ORDINAL,
            'duration' => \NumberFormatter::DURATION,
        ];

        if (!isset($styleValues[$style])) {
            throw new SyntaxError(sprintf('The style "%s" does not exist. Known styles are: "%s"', $style, implode('", "', array_keys($styleValues))));
        }

        $currentStyle = $style;

        return \NumberFormatter::create($locale, $styleValues[$style]);
    }
}
