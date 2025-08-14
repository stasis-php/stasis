<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Route;

/**
 * Group of routes sharing the same root path.
 * Also accepts RouteProviderInterface reference to generate dynamic routes.
 */
readonly class Group implements RouteInterface
{
    public function __construct(
        public string $path,
        /** @var iterable<RouteInterface>|class-string<RouteProviderInterface> */
        public iterable|string $routes = [],
    ) {}

    public function accept(RouteVisitorInterface $visitor): void
    {
        $visitor->visitGroup($this);
    }
}
