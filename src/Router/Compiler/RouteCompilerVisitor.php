<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Vstelmakh\Stasis\Router\Compiler\Resource\ControllerResource;
use Vstelmakh\Stasis\Router\Compiler\Resource\FileResource;
use Vstelmakh\Stasis\Router\Route\Asset;
use Vstelmakh\Stasis\Router\Route\Group;
use Vstelmakh\Stasis\Router\Route\Route;
use Vstelmakh\Stasis\Router\Route\RouteProviderInterface;
use Vstelmakh\Stasis\Router\Route\RouteVisitorInterface;
use Vstelmakh\Stasis\ServiceLocator\ServiceLocator;

class RouteCompilerVisitor implements RouteVisitorInterface
{
    public function __construct(
        private readonly string $basePath,
        private readonly ServiceLocator $serviceLocator,
        public readonly CompiledRouteCollection $routes = new CompiledRouteCollection(),
    ) {}

    public function visitRoute(Route $route): void
    {
        $canonicalPath = $this->getCanonicalPath($route->path);
        $distPath = $canonicalPath . '/index.html';
        $type = new ControllerResource($route->controller, $route->parameters);
        $name = $route->name;

        $compiledRoute = new CompiledRoute($canonicalPath, $distPath, $type, $name);
        $this->routes->add($compiledRoute);
    }

    public function visitGroup(Group $group): void
    {
        $path = $this->getCanonicalPath($group->path);

        if (is_string($group->routes)) {
            $provider = $this->serviceLocator->get($group->routes, RouteProviderInterface::class);
            $routes = $provider->routes();
        } else {
            $routes = $group->routes;
        }

        $visitor = new self($path, $this->serviceLocator, $this->routes);
        foreach ($routes as $route) {
            $route->accept($visitor);
        }
    }

    public function visitAsset(Asset $asset): void
    {
        $canonicalPath = $this->getCanonicalPath($asset->path);
        $resource = new FileResource($asset->sourcePath);
        $name = $asset->name;

        $compiledRoute = new CompiledRoute($canonicalPath, $canonicalPath, $resource, $name);
        $this->routes->add($compiledRoute);
    }

    private function getCanonicalPath(string $path): string
    {
        return PathNormalizer::normalize($this->basePath . '/' . $path);
    }
}
