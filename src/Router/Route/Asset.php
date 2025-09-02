<?php

declare(strict_types=1);

namespace Stasis\Router\Route;

readonly class Asset implements RouteInterface
{
    /**
     * A route mapping to a file or directory, served as static content without processing.
     *
     * @param string $path Route path, starting with slash.
     * @param string $sourcePath Path to the file or directory.
     * @param string|null $name Route name. Must be unique within all defined routes.
     */
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
