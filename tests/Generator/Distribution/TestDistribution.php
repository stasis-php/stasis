<?php

declare(strict_types=1);

namespace Stasis\Tests\Generator\Distribution;

use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\LocalDistributionInterface;
use Stasis\Generator\Distribution\SymlinkDistributionInterface;

class TestDistribution implements DistributionInterface, LocalDistributionInterface, SymlinkDistributionInterface
{
    public private(set) int $cleared = 0;

    /** @var array<int, array{path: string, content: string|resource}> */
    public private(set) array $writes = [];

    /** @var array<int, array{source: string, dest: string}> */
    public private(set) array $copies = [];

    /** @var array<int, array{source: string, dest: string}> */
    public private(set) array $links = [];

    public function clear(): void
    {
        $this->cleared++;
    }

    public function write(string $path, $content): void
    {
        $this->writes[] = ['path' => $path, 'content' => $content];
    }

    public function copy(string $sourcePath, string $destinationPath): void
    {
        $this->copies[] = ['source' => $sourcePath, 'dest' => $destinationPath];
    }

    public function path(): string
    {
        return '/tmp/stasis/test-distribution';
    }

    public function link(string $sourcePath, string $destinationPath): void
    {
        $this->links[] = ['source' => $sourcePath, 'dest' => $destinationPath];
    }
}
