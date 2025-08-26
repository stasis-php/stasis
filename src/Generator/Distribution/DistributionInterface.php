<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator\Distribution;

/**
 * Adapter to manage storage for generated site distributable.
 */
interface DistributionInterface
{
    /**
     * Absolute (local) path to the directory where the distributable located.
     */
    public function path(): string;

    /**
     * Clear the distributable directory.
     */
    public function clear(): void;

    /**
     * Write content to the file in the distributable directory.
     * @param string|resource $content
     */
    public function write(string $path, $content): void;

    /**
     * Copy the file or directory from source to destination in the distributable directory.
     */
    public function copy(string $sourcePath, string $destinationPath): void;

    /**
     * Link the file or directory from source to destination in the distributable directory.
     */
    public function link(string $sourcePath, string $destinationPath): void;
}
