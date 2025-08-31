<?php

declare(strict_types=1);

namespace Stasis\Generator\Distribution;

/**
 * Adapter to manage storage for a generated site distributable with symlink support.
 * Implement this interface to make distribution able to symlink assets on generation.
 */
interface SymlinkDistributionInterface extends DistributionInterface
{
    /**
     * Link the file or directory from source to destination in the distributable directory.
     */
    public function link(string $sourcePath, string $destinationPath): void;
}
