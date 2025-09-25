<?php

declare(strict_types=1);

namespace Stasis\Tests\Router\Compiler;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Stasis\Router\Compiler\PathNormalizer;

class PathNormalizerTest extends TestCase
{
    #[DataProvider('normalizeProvider')]
    public function testNormalize(string $input, string $expected): void
    {
        $result = PathNormalizer::normalize($input);
        self::assertSame($expected, $result);
    }

    public static function normalizeProvider(): array
    {
        return [
            'empty string' => ['', '/'],
            'single slash' => ['/', '/'],
            'double slash' => ['//', '/'],
            'only slashes' => ['///', '/'],
            'already normalized' => ['/a/b', '/a/b'],
            'no leading slash' => ['a/b', '/a/b'],
            'trailing slash' => ['/a/b/', '/a/b'],
            'redundant internal slashes' => ['///a//b///', '/a/b'],
            'mixed slashes' => ['a//b///c', '/a/b/c'],
            'complex' => ['//api///v1//users///', '/api/v1/users'],
        ];
    }
}
