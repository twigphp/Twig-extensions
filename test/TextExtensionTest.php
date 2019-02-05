<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Tests\Extension;

use Twig\Environment;
use Twig\Extensions\TextExtension;

class_exists(TextExtension::class);

class TextExtensionTest extends \PHPUnit\Framework\TestCase
{
    private $env;

    public function setUp()
    {
        $this->env = $this->getMockBuilder(Environment::class)->disableOriginalConstructor()->getMock();
        $this->env
            ->expects($this->any())
            ->method('getCharset')
            ->will($this->returnValue('utf-8'))
        ;
    }

    /**
     * @dataProvider getTruncateTestData
     */
    public function testTruncate($input, $length, $preserve, $separator, $expectedOutput)
    {
        $output = \twig_truncate_filter($this->env, $input, $length, $preserve, $separator);
        $this->assertEquals($expectedOutput, $output);
    }

    public function getTruncateTestData()
    {
        return [
            ['This is a very long sentence.', 2, false, '...', 'Th...'],
            ['This is a very long sentence.', 6, false, '...', 'This i...'],
            ['This is a very long sentence.', 2, true, '...', 'This...'],
            ['This is a very long sentence.', 2, true, '[...]', 'This[...]'],
            ['This is a very long sentence.', 23, false, '...', 'This is a very long sen...'],
            ['This is a very long sentence.', 23, true, '...', 'This is a very long sentence.'],
        ];
    }
}
