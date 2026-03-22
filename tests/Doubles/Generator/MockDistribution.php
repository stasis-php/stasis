<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\Generator;

use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\LocalDistributionInterface;
use Stasis\Generator\Distribution\SymlinkDistributionInterface;

class MockDistribution implements DistributionInterface, LocalDistributionInterface, SymlinkDistributionInterface
{
    public private(set) int $cleared = 0;

    /** @var array<int, array{path: string, content: string|resource}> */
    public private(set) array $writes = [];

    /** @var array<int, array{source: string, dest: string}> */
    public private(set) array $copies = [];

    /** @var array<int, array{source: string, dest: string}> */
    public private(set) array $links = [];

    #[\Override]
    public function clear(): void
    {
        $this->cleared++;
    }

    #[\Override]
    public function write(string $path, $content): void
    {
        $content = is_resource($content) ? stream_get_contents($content) : $content;
        /** @var string $content */
        $this->writes[] = ['path' => $path, 'content' => $content];
    }

    #[\Override]
    public function copy(string $sourcePath, string $destinationPath): void
    {
        $this->copies[] = ['source' => $sourcePath, 'dest' => $destinationPath];
    }

    #[\Override]
    public function path(): string
    {
        return '/tmp/stasis/test-distribution';
    }

    #[\Override]
    public function link(string $sourcePath, string $destinationPath): void
    {
        $this->links[] = ['source' => $sourcePath, 'dest' => $destinationPath];
    }
}
