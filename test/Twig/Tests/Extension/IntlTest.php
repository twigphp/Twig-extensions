<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Extension_IntlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @requires extension intl
     * @requires PHP 5.5
     */
    public function testLocalizedDateFilterWithDateTimeZone()
    {
        class_exists('Twig_Extensions_Extension_Intl');
        $env = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $date = twig_localized_date_filter($env, new DateTime('2015-01-01T00:00:00', new DateTimeZone('UTC')), 'short', 'long', 'en', '+01:00');
        $this->assertEquals('1/1/15, 12:00:00 AM GMT', $date);
    }

    /**
     * @requires extension intl
     * @requires PHP 5.5
     */
    public function testLocalizedDateFilterWithDateTimeZoneZ()
    {
        class_exists('Twig_Extensions_Extension_Intl');
        $env = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $date = twig_localized_date_filter($env, new DateTime('2017-11-19T00:00:00Z'), 'short', 'long', 'fr', 'Z');
        $this->assertEquals('19/11/2017 00:00:00 UTC', $date);
    }

    /**
     * @requires extension intl
     * @dataProvider getLocalizedCurrencyFilterWithFractionDigitsTestData
     */
    public function testLocalizedCurrencyFilterWithFractionDigits($value, $currency, $fractionDigits, $expectedOutput)
    {
        class_exists('Twig_Extensions_Extension_Intl');

        $output = twig_localized_currency_filter($value, $currency, $fractionDigits);

        $this->assertEquals($expectedOutput, $output);
    }

    public function getLocalizedCurrencyFilterWithFractionDigitsTestData()
    {
        return array(
            array(M_PI, 'USD', 0, '$3'),
            array(M_PI, 'USD', 2, '$3.14'),
            array(M_PI, 'USD', 4, '$3.1416'),
        );
    }
}
