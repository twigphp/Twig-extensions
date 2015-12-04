<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__.'/../../../../lib/Twig/Extensions/Extension/Link.php';

/**
 * @author Jules Pietri <jules@heahprod.com>
 */
class Twig_Tests_Extension_LinkTest extends PHPUnit_Framework_TestCase
{
    private $env;

    public static function setUpBeforeClass()
    {
        if (!class_exists('Twig_Extensions_Extension_Link')) {
            self::markTestSkipped('Unable to find class Twig_Extensions_Extension_Link.');
        }
    }

    public function setUp()
    {
        $coreExtension = $this->getMock('Twig_Extension_Core');

        $this->env = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $this->env
            ->expects($this->any())
            ->method('getExtension')
            ->with('core')
            ->will($this->returnValue($coreExtension))
        ;
    }

    /**
     * @dataProvider getLinks()
     */
    public function testCreateLink($url, $attributes, $withScheme, $expectedOutput)
    {
        $extension = new Twig_Extensions_Extension_Link();
        $this->assertEquals($expectedOutput, $extension->createLink($url, $attributes, $withScheme));
    }

    /**
     * @dataProvider getNamedLinks()
     */
    public function testCreateNamedLink($url, $link, $attributes, $expectedOutput)
    {
        $extension = new Twig_Extensions_Extension_Link();
        $this->assertEquals($expectedOutput, $extension->createNamedLink($url, $link, $attributes));
    }

    /**
     * @dataProvider getMails()
     */
    public function testCreateMailTo($mail, $attributes, $subject, $body, $expectedOutput)
    {
        $extension = new Twig_Extensions_Extension_Link();
        $this->assertEquals($expectedOutput, $extension->createMailTo($mail, $attributes, $subject, $body));
    }

    /**
     * @dataProvider getNamedMails()
     */
    public function testCreateNamedMailTo($mail, $name, $attributes, $subject, $body, $expectedOutput)
    {
        $extension = new Twig_Extensions_Extension_Link();
        $this->assertEquals($expectedOutput, $extension->createNamedMailTo($mail, $name, $attributes, $subject, $body));
    }

    public function getLinks()
    {
        return array(
            array(
                'twig.sensiolabs.org/documentation', array(), false,
                '<a href="http://twig.sensiolabs.org/documentation">twig.sensiolabs.org/documentation</a>'
            ),
            array(
                'https://github.com/twigphp/Twig', array(), false,
                '<a href="https://github.com/twigphp/Twig">github.com/twigphp/Twig</a>'
            ),
            array(
                'twig.sensiolabs.org/documentation', array(), true,
                '<a href="http://twig.sensiolabs.org/documentation">http://twig.sensiolabs.org/documentation</a>'
            ),
            array(
                'https://github.com/twigphp/Twig', array(), true,
                '<a href="https://github.com/twigphp/Twig">https://github.com/twigphp/Twig</a>'
            ),
            array(
                'twig.sensiolabs.org/documentation', array('class' => 'mylinks', 'target' => '_blank'), false,
                '<a href="http://twig.sensiolabs.org/documentation" class="mylinks" target="_blank">twig.sensiolabs.org/documentation</a>'
            ),
            array('https://github.com/twigphp/Twig', array('class' => 'mylinks', 'target' => '_blank'), false,
                '<a href="https://github.com/twigphp/Twig" class="mylinks" target="_blank">github.com/twigphp/Twig</a>'
            ),
            array('twig.sensiolabs.org/documentation', array('class' => 'mylinks', 'target' => '_blank'), true,
                '<a href="http://twig.sensiolabs.org/documentation" class="mylinks" target="_blank">http://twig.sensiolabs.org/documentation</a>'
            ),
            array('https://github.com/twigphp/Twig', array('class' => 'mylinks', 'target' => '_blank'), true,
                '<a href="https://github.com/twigphp/Twig" class="mylinks" target="_blank">https://github.com/twigphp/Twig</a>'
            )
        );
    }

    public function getNamedLinks()
    {
        return array(
            array('twig.sensiolabs.org/documentation', 'Twig Documentation', array(),
                '<a href="http://twig.sensiolabs.org/documentation">Twig Documentation</a>'
            ),
            array('https://github.com/twigphp/Twig', 'Twig on GitHub', array(),
                '<a href="https://github.com/twigphp/Twig">Twig on GitHub</a>'
            ),
            array('twig.sensiolabs.org/documentation', 'Twig Documentation', array('class' => 'mylinks', 'target' => '_blank'),
                '<a href="http://twig.sensiolabs.org/documentation" class="mylinks" target="_blank">Twig Documentation</a>'
            ),
            array('https://github.com/twigphp/Twig', 'Twig on GitHub', array('class' => 'mylinks', 'target' => '_blank'),
                '<a href="https://github.com/twigphp/Twig" class="mylinks" target="_blank">Twig on GitHub</a>'
            )
        );
    }

    public function getMails()
    {
        return array(
            array(
                'fabien@symfony.com', array(), '', '',
                '<a href="mailto:fabien@symfony.com">fabien@symfony.com</a>'
            ),
            array(
                'fabien@symfony.com', array('class' => 'mail-links'), '', '',
                '<a href="mailto:fabien@symfony.com" class="mail-links">fabien@symfony.com</a>'
            ),
            array(
                'fabien@symfony.com', array(), 'What about Twig ?', '',
                '<a href="mailto:fabien@symfony.com?subject=What%20about%20Twig%20%3F">fabien@symfony.com</a>'
            ),
            array(
                'fabien@symfony.com', array(), '', 'Thank you for bringing PHP to a higher level for 10 years',
                '<a href="mailto:fabien@symfony.com?body=Thank%20you%20for%20bringing%20PHP%20to%20a%20higher%20level%20for%2010%20years">fabien@symfony.com</a>'
            ),
            array(
                'fabien@symfony.com', array('class' => 'mail-links'), 'What about Twig ?', 'Thank you for bringing PHP to a higher level for 10 years',
                '<a href="mailto:fabien@symfony.com?subject=What%20about%20Twig%20%3F&body=Thank%20you%20for%20bringing%20PHP%20to%20a%20higher%20level%20for%2010%20years" class="mail-links">fabien@symfony.com</a>'
            ),
        );
    }

    public function getNamedMails()
    {
        return array(
            array(
                'fabien@symfony.com', 'Fabien', array(), '', '',
                '<a href="mailto:fabien@symfony.com">Fabien</a>'
            ),
            array(
                'fabien@symfony.com', 'Fabien', array('class' => 'mail-links'), '', '',
                '<a href="mailto:fabien@symfony.com" class="mail-links">Fabien</a>'
            ),
            array(
                'fabien@symfony.com', 'Fabien', array(), 'What about Twig ?', '',
                '<a href="mailto:fabien@symfony.com?subject=What%20about%20Twig%20%3F">Fabien</a>'
            ),
            array(
                'fabien@symfony.com', 'Fabien', array(), '', 'Thank you for bringing PHP to a higher level for 10 years',
                '<a href="mailto:fabien@symfony.com?body=Thank%20you%20for%20bringing%20PHP%20to%20a%20higher%20level%20for%2010%20years">Fabien</a>'
            ),
            array(
                'fabien@symfony.com', 'Fabien', array('class' => 'mail-links'), 'What about Twig ?', 'Thank you for bringing PHP to a higher level for 10 years',
                '<a href="mailto:fabien@symfony.com?subject=What%20about%20Twig%20%3F&body=Thank%20you%20for%20bringing%20PHP%20to%20a%20higher%20level%20for%2010%20years" class="mail-links">Fabien</a>'
            ),
        );
    }
}