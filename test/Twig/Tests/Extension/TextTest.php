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

require_once __DIR__.'/../../../../lib/Twig/Extensions/Extension/Text.php';

class Twig_Tests_Extension_TextTest extends TestCase
{
    /** @var Environment */
    private $env;

    public function setUp(): void
    {
        $this->env = $this->getMockBuilder(Environment::class)->disableOriginalConstructor()->getMock();
        $this->env
            ->method('getCharset')
            ->willReturn('utf-8');
    }

    /**
     * @dataProvider getTruncateTestData
     * @param string $input
     * @param int $length
     * @param bool $preserve
     * @param string $separator
     * @param string $expectedOutput
     */
    public function testTruncate(
        string $input,
        int $length,
        bool $preserve,
        string $separator,
        string $expectedOutput
    ): void {
        $output = twig_truncate_filter($this->env, $input, $length, $preserve, $separator);
        self::assertEquals($expectedOutput, $output);
    }

    public function getTruncateTestData(): array
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
