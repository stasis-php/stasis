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
        $kernelPath = new \ReflectionClass(Kernel::class)->getFileName();

        if ($kernelPath === false) {
            throw new \RuntimeException('Failed to get kernel path.');
        }

        return dirname($kernelPath, 2) . '/bin/stasis';
    }
}
