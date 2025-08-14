<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router;

use Vstelmakh\Stasis\Router\Compiler\CompiledRoute;
use Vstelmakh\Stasis\Router\Compiler\CompiledRoutes;
use Vstelmakh\Stasis\Router\Compiler\RouteCompiler;
use Vstelmakh\Stasis\Router\Route\RouteInterface;

class Router
{
    private CompiledRoutes $compiledRoutes;

    /**
     * @param iterable<RouteInterface> $routes
     */
    public function __construct(iterable $routes, RouteCompiler $compiler)
    {
        $this->compiledRoutes = new CompiledRoutes();
        $compiler->compile($routes);
    }

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
