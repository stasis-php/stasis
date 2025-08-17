<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator\Distribution;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Vstelmakh\Stasis\Exception\LogicException;
use Vstelmakh\Stasis\Exception\RuntimeException;

class FilesystemDistribution implements DistributionInterface
{
    private readonly string $basePath;
    private Filesystem $filesystem;

    public function __construct(string $distPath)
    {
        if (!Path::isAbsolute($distPath)) {
            throw new LogicException(sprintf('Provided dist path "%s" is not absolute.', $distPath));
        }
        $this->basePath = rtrim(Path::canonicalize($distPath), '/');
        $this->filesystem = new Filesystem();
    }

    public function clear(): void
    {
        try {
            $this->filesystem->remove($this->basePath);
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                message: sprintf('Error clearing distribution "%s".', $this->basePath),
                previous: $exception
            );
        }
    }

    public function write(string $path, $content): void
    {
        $fullPath = $this->getFullPath($path);
        $this->filesystem->dumpFile($fullPath, $content);

        try {
            $this->filesystem->dumpFile($fullPath, $content);
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                message: sprintf('Error writing to distribution path "%s".', $fullPath),
                previous: $exception
            );
        }
    }

    private function getFullPath(string $path): string
    {
        return sprintf('%s/%s', $this->basePath, ltrim($path, '/'));
    }
}
