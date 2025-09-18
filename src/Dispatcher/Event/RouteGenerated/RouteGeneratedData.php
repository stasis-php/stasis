<?php

declare(strict_types=1);

namespace Stasis\Dispatcher\Event\RouteGenerated;

use Stasis\Router\Compiler\CompiledRoute;

readonly class RouteGeneratedData
{
    public function __construct(
        public CompiledRoute $route,
    ) {}
}
