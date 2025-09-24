<?php

declare(strict_types=1);

namespace Stasis\Stopwatch;

class Stopwatch
{
    private float $startTime;

    public function __construct()
    {
        $this->startTime = $this->microtime();
    }

    public function duration(int $precision = 3): float
    {
        return round($this->microtime() - $this->startTime, $precision);
    }

    public function memory(): int
    {
        return memory_get_peak_usage(true);
    }

    private function microtime(): float
    {
        return microtime(true);
    }
}
