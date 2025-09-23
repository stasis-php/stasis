<?php

declare(strict_types=1);

namespace Stasis\Generator\Distribution;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Stasis\Exception\LogicException;
use Stasis\Exception\RuntimeException;

class FilesystemDistribution implements LocalDistributionInterface, SymlinkDistributionInterface
{
    private readonly string $basePath;
    private Filesystem $filesystem;

    public function __construct(string $distPath, Filesystem $filesystem = new Filesystem())
    {
        if (!Path::isAbsolute($distPath)) {
            throw new LogicException(sprintf('Provided dist path "%s" is not absolute.', $distPath));
        }
        $this->basePath = rtrim(Path::canonicalize($distPath), '/');
        $this->filesystem = $filesystem;
    }

    public function path(): string
    {
        return $this->basePath;
    }

    public function clear(): void
    {
        try {
            if (!$this->filesystem->exists($this->basePath) || !is_dir($this->basePath)) {
                return;
            }

            $iterator = new \FilesystemIterator($this->basePath, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);
            foreach ($iterator as $path) {
                $this->filesystem->remove($path);
            }
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                message: sprintf('Error clearing distribution "%s".', $this->basePath),
                previous: $exception,
            );
        }
    }

    public function write(string $path, $content): void
    {
        $fullPath = $this->getFullPath($path);
        try {
            $this->filesystem->dumpFile($fullPath, $content);
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                message: sprintf('Error writing to distribution path "%s".', $fullPath),
                previous: $exception,
            );
        }
    }

    public function copy(string $sourcePath, string $destinationPath): void
    {
        if (!file_exists($sourcePath)) {
            throw new LogicException(sprintf('Source path "%s" does not exist', $sourcePath));
        }

        $isDir = is_dir($sourcePath);
        $fullPath = $this->getFullPath($destinationPath);

        try {
            if ($isDir) {
                $this->filesystem->mirror($sourcePath, $fullPath);
            } else {
                $this->filesystem->copy($sourcePath, $fullPath, true);
            }
        } catch (\Throwable $exception) {
            $type = $isDir ? 'directory' : 'file';
            throw new RuntimeException(
                message: sprintf('Error copying %s "%s" to distribution "%s".', $type, $sourcePath, $fullPath),
                previous: $exception,
            );
        }
    }

    public function link(string $sourcePath, string $destinationPath): void
    {
        if (!file_exists($sourcePath)) {
            throw new LogicException(sprintf('Source path "%s" does not exist', $sourcePath));
        }

        $fullPath = $this->getFullPath($destinationPath);

        try {
            $this->filesystem->symlink($sourcePath, $fullPath, false);
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                message: sprintf('Error creating symlink "%s" to distribution "%s".', $sourcePath, $fullPath),
                previous: $exception,
            );
        }
    }

    private function getFullPath(string $path): string
    {
        return sprintf('%s/%s', $this->basePath, ltrim($path, '/'));
    }
}
