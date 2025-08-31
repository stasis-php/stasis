<?php

declare(strict_types=1);

namespace Stasis\Generator\Distribution;

/**
 * Adapter to manage local storage for generated site distributable.
 * Implement this interface to make distribution usable for development web server.
 */
interface LocalDistributionInterface extends DistributionInterface
{
    /**
     * Absolute (local) path to the directory where the distributable located.
     */
    public function path(): string;
}
