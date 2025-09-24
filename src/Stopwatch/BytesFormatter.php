<?php

declare(strict_types=1);

namespace Stasis\Stopwatch;

class BytesFormatter
{
    public static function toHuman(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = 1024;
        $exponent = (int) floor(log($bytes, $base));
        $pow = min($exponent, count($units) - 1);
        $value = $bytes / $base ** $pow;

        $format = $pow > 0 ? '%.2f %s' : '%d %s';
        return sprintf($format, $value, $units[$pow]);
    }
}
