<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @requires extension gettext
 */
class Twig_Tests_Extension_I18nTest extends PHPUnit_Framework_TestCase
{
    private $domain = 'test';

    public static function setUpBeforeClass()
    {
        if (!class_exists('Twig_Extensions_Extension_I18n')) {
            self::markTestSkipped('Unable to find class Twig_Extensions_Extension_I18n.');
        }
    }

    public function setUp()
    {
        setlocale(LC_ALL, 'en_US.UTF-8');
        bindtextdomain($this->domain, __DIR__.'/../../../locale');
        bind_textdomain_codeset($this->domain, 'UTF-8');
        textdomain($this->domain);
    }

    /**
     * @dataProvider getTransTestData
     */
    public function testTrans($string, $expected, $domain = null)
    {
        $extension = new Twig_Extensions_Extension_I18n();

        $output = $extension->trans($string, $domain);
        $this->assertEquals($expected, $output);
    }

    public function getTransTestData()
    {
        return array(
            array(gettext('Translate this'), 'into this.'),
            array(dgettext($this->domain, 'Translate this'), 'into this.', $this->domain),
        );
    }
}
