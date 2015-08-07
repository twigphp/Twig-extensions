<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__.'/../../../../lib/Twig/Extensions/Extension/Intl.php';

/**
 * @author Remy Gazelot <r.gazelot@gmail.com>
 */
class Twig_Tests_Extension_IntlTest extends PHPUnit_Framework_TestCase
{
    public function testLocalizedDateFilterWithDateTimeZone()
    {
        if (PHP_VERSION_ID < 50500) {
            $this->markTestSkipped('Only in PHP 5.5+ IntlDateFormatter allows to use DateTimeZone objects.');
        }

        if (defined('HHVM_VERSION_ID')) {
            $this->markTestSkipped('This test cannot work on HHVM. See https://github.com/facebook/hhvm/issues/5875 for the issue.');
        }

        $date = twig_localized_date_filter(
            $this->env = $this->getMock('Twig_Environment'),
            new DateTime('2015-01-01T00:00:00', new DateTimeZone('UTC')),
            'short',
            'long',
            'en',
            '+02:00'
        );

        $this->assertEquals('1/1/15 2:00:00 AM GMT+02:00', $date);
    }
}
