<?php

declare(strict_types=1);

namespace Stasis\Router\Compiler;

/**
 * @internal
 */
class PathNormalizer
{
    public static function normalize(string $path): string
    {
        $path = self::mergeRedundantSlashes($path);
        $path = self::trimSlashes($path);
        return '/' . $path;
    }

    private static function mergeRedundantSlashes(string $path): string
    {
        return (string) preg_replace('/\/{2,}/', '/', $path);
    }

    private static function trimSlashes(string $path): string
    {
        return trim($path, '/');
    }
}
