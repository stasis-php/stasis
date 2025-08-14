<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator\Distribution;

/**
 * Adapter to manage storage for generated site distributable.
 */
interface DistributionInterface
{
    public function clear(): void;

    /**
     * @param string|resource $content
     */
    public function write(string $path, $content): void;
}
