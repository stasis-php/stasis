<?php

declare(strict_types=1);

namespace Stasis\Stopwatch;

class Formatter
{
    public static function bytesToHuman(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = 1024;
        $exponent = (int) floor(log($bytes, $base));
        $pow = min($exponent, count($units) - 1);
        $value = $bytes / $base ** $pow;

        $format = $pow > 0 ? '%.2f %s' : '%d %s';
        return sprintf($format, $value, $units[$pow]);
    }

    public static function secondsToHuman(float $seconds): string
    {
        $units = ['seconds', 'minutes', 'hours'];
        $base = 60;
        $exponent = (int) floor(log($seconds, $base));
        $pow = min($exponent, count($units) - 1);
        $pow = max($pow, 0);
        $value = $seconds / $base ** $pow;
        return sprintf('%.2f %s', $value, $units[$pow]);
    }
}
