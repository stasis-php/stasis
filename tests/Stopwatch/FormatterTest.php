<?php

declare(strict_types=1);

namespace Stasis\Tests\Stopwatch;

use PHPUnit\Framework\Attributes\DataProvider;
use Stasis\Stopwatch\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    #[DataProvider('bytesProvider')]
    public function testBytesToHuman(int $bytes, string $expected): void
    {
        $result = Formatter::bytesToHuman($bytes);
        self::assertSame($expected, $result);
    }

    public static function bytesProvider(): array
    {
        return [
            'bytes' => [123, '123 B'],
            'kilobytes' => [1024, '1.00 KB'],
            'megabytes' => [1024 * 1024, '1.00 MB'],
            'gigabytes' => [1024 * 1024 * 1024, '1.00 GB'],
            'terabytes' => [1024 * 1024 * 1024 * 1024, '1.00 TB'],
            'maximum unit' => [1024 * 1024 * 1024 * 1024 * 1024, '1024.00 TB'],
            'floating point kb' => [1536, '1.50 KB'],
            'floating point mb' => [2467935, '2.35 MB'],
        ];
    }

    #[DataProvider('secondsProvider')]
    public function testSecondsToHuman(float $seconds, string $expected): void
    {
        $result = Formatter::secondsToHuman($seconds);
        self::assertSame($expected, $result);
    }

    public static function secondsProvider(): array
    {
        return [
            'seconds' => [1, '1.00 seconds'],
            'minutes' => [60, '1.00 minutes'],
            'hours' => [3600, '1.00 hours'],
            'maximum unit' => [86400, '24.00 hours'],
            'floating point seconds' => [1.53, '1.53 seconds'],
            'floating point minutes' => [95, '1.58 minutes'],
            'floating point hours' => [4900.5, '1.36 hours'],
        ];
    }
}
