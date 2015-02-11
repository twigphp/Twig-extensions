<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__ . '/../../../../lib/Twig/Extensions/Extension/Intl.php';

class Twig_Tests_Extension_IntlTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        if (!class_exists('Twig_Extensions_Extension_Intl')) {
            self::markTestSkipped('Unable to find class Twig_Extensions_Extension_Intl.');
        }
        if (!function_exists('twig_localized_currency_filter')) {
            self::markTestSkipped('Unable to find twig_localized_currency_filter function.');
        }
    }

    /**
     * @dataProvider getCurrencyData
     */
    public function testCurrencyFilter($expected, $number, $currency = null, $locale = null, $calculatePrecision=false)
    {
        $this->assertEquals(
            $expected,
            twig_localized_currency_filter($number, $currency, $locale, $calculatePrecision)
        );
    }

    /**
     * @dataProvider getNumberData
     */
    public function testNumberFilter($expected, $number, $style = 'decimal', $type = 'default', $locale = null, $calculatePrecision=false)
    {
        $this->assertEquals(
            $expected,
            twig_localized_number_filter($number, $style, $type, $locale, $calculatePrecision)
        );
    }

    public function getCurrencyData()
    {
        return array(

            // Not passing a currency in TEST will result in false
            array(
                false,
                15.00,
            ),
            array(
                "€15.00",
                15.00,
                'EUR',
            ),

            // Test locale
            array(
                "15,00 €",
                15.0023,
                'EUR',
                'de_DE',
            ),

            // Test calculatePrecision
            array(
                "€15.00",
                15.0023,
                'EUR',
                null,
                false,
            ),
            array(
                "€15.0023",
                15.0023,
                'EUR',
                null,
                true,
            ),
            array(
                "15,0023 €",
                15.0023,
                'EUR',
                'de_DE',
                true
            ),
        );
    }

    public function getNumberData()
    {
        return array(
            array(
                15,
                15.00,
            ),
            array(
                15.00,
                15.00,
            ),
            array(
                '$15.00',
                15.00,
                'currency'
            ),

            // calculatePrecision
            array(
                15.002, // Three decimals by default
                15.0023,
                'decimal',
                'default',
                null,
                false,
            ),
            array(
                15.0023, // More decimals when precision is calculated
                15.0023,
                'decimal',
                'default',
                null,
                true,
            ),
        );
    }
}