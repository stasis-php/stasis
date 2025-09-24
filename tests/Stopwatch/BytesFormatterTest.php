<?php

declare(strict_types=1);

namespace Stasis\Tests\Stopwatch;

use PHPUnit\Framework\Attributes\DataProvider;
use Stasis\Stopwatch\BytesFormatter;
use PHPUnit\Framework\TestCase;

class BytesFormatterTest extends TestCase
{
    #[DataProvider('bytesProvider')]
    public function testToHuman(int $bytes, string $expected): void
    {
        $result = BytesFormatter::toHuman($bytes);
        $this->assertSame($expected, $result);
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
}
