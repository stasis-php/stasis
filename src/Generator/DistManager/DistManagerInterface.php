<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator\DistManager;

/**
 * Adapter to manage storage for generated site distributable.
 */
interface DistManagerInterface
{
    public function clear(): void;

    /**
     * @param string|resource $content
     */
    public function write(string $path, $content): void;
}
