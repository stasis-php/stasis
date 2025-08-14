<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator\DistManager;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Vstelmakh\Stasis\Exception\LogicException;

class FilesystemDistManager implements DistManagerInterface
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
        $this->filesystem->remove($this->basePath);
    }

    public function write(string $path, $content): void
    {
        $fullPath = $this->getFullPath($path);
        $this->filesystem->dumpFile($fullPath, $content);
    }

    private function getFullPath(string $path): string
    {
        return sprintf('%s/%s', $this->basePath, $path);
    }
}
