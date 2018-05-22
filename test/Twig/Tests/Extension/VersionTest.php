<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../../../../lib/Twig/Extensions/Extension/Version.php';

class Twig_Tests_Extension_VersionTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        if (PHP_VERSION_ID < 70000) {
            $this->markTestSkipped('Version extension tests require PHP 7.0 and later.');
        }
    }

    public function testPackageVersion()
    {
        $output = twig_package_version('twig/twig');
        $this->assertInternalType('string', $output);
        $this->assertNotEmpty($output);
    }

    public function testPrettyPackageVersion()
    {
        $output = twig_pretty_package_version('twig/twig');
        $this->assertInstanceOf('Jean85\Version', $output);
    }
}
