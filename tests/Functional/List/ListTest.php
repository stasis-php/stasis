<?php

declare(strict_types=1);

namespace Stasis\Tests\Functional\List;

use PHPUnit\Framework\TestCase;
use Stasis\Tests\Functional\StasisProcessFactory;

class ListTest extends TestCase
{
    public function testList(): void
    {
        $stasis = StasisProcessFactory::create();
        $stasis->run();
        $exitCode = $stasis->getExitCode();
        $output = $stasis->getOutput();

        self::assertSame(0, $exitCode, 'Command returned non-zero exit code.');

        self::assertStringContainsString(
            'Available commands',
            $output,
            'Missing command list in output.',
        );

        self::assertStringContainsString(
            'Generate static site from specified routes',
            $output,
            'Missing generate command in output.',
        );
    }
}
