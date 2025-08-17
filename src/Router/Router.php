<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router;

use Vstelmakh\Stasis\Router\Compiler\CompiledRoute;
use Vstelmakh\Stasis\Router\Compiler\CompiledRouteCollection;

class Router
{
    public function __construct(
        private readonly CompiledRouteCollection $compiledRoutes,
    ) {}

    public function get(string $name): CompiledRoute
    {
        return $this->compiledRoutes->getByName($name);
    }

    /**
     * @return array<CompiledRoute>
     */
    public function all(): array
    {
        return  $this->compiledRoutes->all();
    }
}
