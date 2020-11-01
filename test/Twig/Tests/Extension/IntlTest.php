<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Twig\Environment;

class Twig_Tests_Extension_IntlTest extends TestCase
{
    /**
     * @requires extension intl
     * @requires PHP 5.5
     * @throws Exception
     */
    public function testLocalizedDateFilterWithDateTimeZone(): void
    {
        class_exists('Twig_Extensions_Extension_Intl');

        /** @var Environment $env */
        $env = $this->getMockBuilder(Environment::class)->disableOriginalConstructor()->getMock();
        $date = twig_localized_date_filter(
            $env,
            new DateTime('2015-01-01T00:00:00', new DateTimeZone('UTC')),
            'short',
            'long',
            'en',
            '+01:00'
        );
        self::assertEquals('1/1/15, 12:00:00 AM GMT', $date);
    }

    /**
     * @requires extension intl
     * @requires PHP 5.5
     */
    public function testLocalizedDateFilterWithDateTimeZoneZ(): void
    {
        class_exists('Twig_Extensions_Extension_Intl');

        /** @var Environment $env */
        $env = $this->getMockBuilder(Environment::class)->disableOriginalConstructor()->getMock();
        $date = twig_localized_date_filter($env, new DateTime('2017-11-19T00:00:00Z'), 'short', 'long', 'fr', 'Z');
        self::assertEquals('19/11/2017 00:00:00 UTC', $date);
    }
}
