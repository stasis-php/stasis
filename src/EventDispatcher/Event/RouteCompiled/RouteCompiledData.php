<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Event\RouteCompiled;

use Stasis\Router\Compiler\CompiledRoute;

class RouteCompiledData
{
    /**
     * @internal
     */
    public function __construct(
        public CompiledRoute $route,
    ) {}
}
