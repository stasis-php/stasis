<?php

declare(strict_types=1);

namespace Stasis\Tests\Functional\List;

use PHPUnit\Framework\TestCase;
use Stasis\Tests\Functional\StasisRunner;

class ListTest extends TestCase
{
    private StasisRunner $runner;

    public function setUp(): void
    {
        $this->runner = new StasisRunner();
    }

    public function testList(): void
    {
        $result = $this->runner->run('');

        self::assertSame(0, $result->exitCode, 'Command returned non-zero exit code.');

        self::assertStringContainsString(
            'Available commands',
            $result->output,
            'Missing command list in output.',
        );

        self::assertStringContainsString(
            'Generate static site from specified routes',
            $result->output,
            'Missing generate command in output.',
        );
    }
}
