<?php

declare(strict_types=1);

namespace Stasis\Tests\Functional;

use Stasis\Kernel;

class StasisRunner
{
    public function run(string $arguments): StasisRunResult
    {
        $stasisPath = $this->getStasisPath();
        $command = trim(sprintf('%s %s', $stasisPath, $arguments));
        exec($command . ' 2>&1', $output, $exitCode);
        return new StasisRunResult($command, implode("\n", $output), $exitCode);
    }

    private function getStasisPath(): string
    {
        /** @var string $kernelPath */
        $kernelPath = new \ReflectionClass(Kernel::class)->getFileName();
        return dirname($kernelPath, 2) . '/bin/stasis';
    }
}
