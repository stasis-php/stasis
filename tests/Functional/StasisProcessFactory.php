<?php

declare(strict_types=1);

namespace Stasis\Tests\Functional;

use Stasis\Kernel;
use Symfony\Component\Process\Process;

class StasisProcessFactory
{
    /**
     * @param array<string> $arguments
     */
    public static function create(array $arguments = [], ?string $workdir = null): Process
    {
        $stasisPath = self::getStasisPath();
        $command = [$stasisPath, ...$arguments];
        return new Process($command, $workdir);
    }

    private static function getStasisPath(): string
    {
        /** @var string $kernelPath */
        $kernelPath = new \ReflectionClass(Kernel::class)->getFileName();
        return dirname($kernelPath, 2) . '/bin/stasis';
    }
}
