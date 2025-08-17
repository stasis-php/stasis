<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Psr\Container\ContainerInterface;
use Vstelmakh\Stasis\Router\Route\RouteInterface;

class RouteCompiler
{
    public function __construct(
        private readonly string $basePath,
        private readonly ContainerInterface $container,
    ) {}

    /**
     * @param iterable<RouteInterface> $routes
     */
    public function compile(iterable $routes): CompiledRouteCollection
    {
        $visitor = new RouteCompilerVisitor($this->basePath, $this->container);

        foreach ($routes as $route) {
            $route->accept($visitor);
        }

        return $visitor->routes;
    }
}
