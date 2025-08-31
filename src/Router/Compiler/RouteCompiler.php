<?php

declare(strict_types=1);

namespace Stasis\Router\Compiler;

use Stasis\Router\Route\RouteInterface;
use Stasis\ServiceLocator\ServiceLocator;

class RouteCompiler
{
    public function __construct(
        private readonly string $basePath,
        private readonly ServiceLocator $serviceLocator,
    ) {}

    /**
     * @param iterable<RouteInterface> $routes
     */
    public function compile(iterable $routes): CompiledRouteCollection
    {
        $visitor = new RouteCompilerVisitor($this->basePath, $this->serviceLocator);

        foreach ($routes as $route) {
            $route->accept($visitor);
        }

        return $visitor->routes;
    }
}
