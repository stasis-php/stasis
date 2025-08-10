<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Psr\Container\ContainerInterface;
use Vstelmakh\Stasis\Router\Group;
use Vstelmakh\Stasis\Router\Route;
use Vstelmakh\Stasis\Router\RouteInterface;
use Vstelmakh\Stasis\Router\RouteProviderInterface;
use Vstelmakh\Stasis\Router\RouteVisitorInterface;

/**
 * Transforming various route types to CompiledRoute. Resolving absolute paths and dynamic routes.
 */
class RouteCompiler implements RouteVisitorInterface
{
    private CompiledRoutes $compiledRoutes;

    public function __construct(
        private readonly string $basePath,
        private readonly ContainerInterface $container,
    ) {
        $this->compiledRoutes = new CompiledRoutes();
    }

    /**
     * @param iterable<RouteInterface> $routes
     * @throws CompilationFailedException
     */
    public function compile(iterable $routes): CompiledRoutes
    {
        $this->compiledRoutes = new CompiledRoutes();

        foreach ($routes as $route) {
            $route->accept($this);
        }

        return $this->compiledRoutes;
    }

    public function visitRoute(Route $route): void
    {
        $path = $this->getCanonicalPath($route->path);
        $distPath = $this->getDistPath($path);

        $compiledRoute = new CompiledRoute(
            $path ?: '/',
            $distPath,
            $route->controller,
            $route->name,
            $route->parameters,
        );

        $this->compiledRoutes->add($compiledRoute);
    }

    /**
     * @throws CompilationFailedException
     */
    public function visitGroup(Group $group): void
    {
        $path = $this->getCanonicalPath($group->path);

        if (is_string($group->routes)) {
            $providerId = $group->routes;
            $routeData = ['path' => $path, 'type' => 'group'];

            try {
                $provider = $this->container->get($providerId);
            } catch (\Throwable $exception) {
                throw new CompilationFailedException(
                    sprintf(
                        'Unable to resolve route provider "%s" from container.',
                        $providerId,
                    ),
                    $routeData,
                    $exception,
                );
            }

            if (!$provider instanceof RouteProviderInterface) {
                throw new CompilationFailedException(
                    sprintf(
                        'Provider "%s" is not implementing "%s".',
                        $providerId,
                        RouteProviderInterface::class,
                    ),
                    $routeData,
                );
            }

            $routes = $provider->routes();
        } else {
            $routes = $group->routes;
        }

        $compiler = new self($path, $this->container);
        $collection = $compiler->compile($routes);

        foreach ($collection->getAll() as $compiledRoute) {
            $this->compiledRoutes->add($compiledRoute);
        }
    }

    private function getCanonicalPath(string $path): string
    {
        $path = $this->basePath . $path;
        $path = preg_replace('/\/{2,}/', '/', $path);
        return rtrim($path, '/');
    }

    private function getDistPath(string $path): string
    {
        return sprintf('%s/index.html', $path);
    }
}
