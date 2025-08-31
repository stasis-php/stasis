<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Traversable;

/**
 * @implements \IteratorAggregate<int, CompiledRoute>
 */
class CompiledRouteCollection implements \IteratorAggregate
{
    /** @var array<string, CompiledRoute> */
    private array $routeByPath = [];

    /** @var array<string, CompiledRoute> */
    private array $routeByName = [];

    public function getByName(string $name): ?CompiledRoute
    {
        return $this->routeByName[$name] ?? null;
    }

    /**
     * @return array<CompiledRoute>
     */
    public function all(): array
    {
        return array_values($this->routeByPath);
    }

    public function add(CompiledRoute $route): self
    {
        $this->addToPathMap($route);
        $this->addToNameMap($route);
        return $this;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->all());
    }

    private function addToPathMap(CompiledRoute $route): void
    {
        $path = $route->path;

        if (isset($this->routeByPath[$path])) {
            throw new \LogicException(sprintf('Duplicated route path "%s".', $path));
        }

        $this->routeByPath[$path] = $route;
    }

    private function addToNameMap(CompiledRoute $route): void
    {
        $name = $route->name;

        if ($name === null) {
            return;
        }

        if (isset($this->routeByName[$name])) {
            throw new \LogicException(sprintf('Duplicated route name "%s".', $name));
        }

        $this->routeByName[$name] = $route;
    }
}
