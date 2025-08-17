<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Route;

/**
 * Route that represents the file or directory of files that will be served as is.
 */
readonly class Asset implements RouteInterface
{
    public function __construct(
        public string $path,
        public string $sourcePath,
        public ?string $name = null,
    ) {}

    public function accept(RouteVisitorInterface $visitor): void
    {
        $visitor->visitAsset($this);
    }
}
