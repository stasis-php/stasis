<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router;

/**
 * Group of routes sharing the same root path. Also, can generate dynamic routes with RouteProviderInterface.
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
