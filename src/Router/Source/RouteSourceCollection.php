<?php

declare(strict_types=1);

namespace Stasis\Router\Source;

use Stasis\Exception\LogicException;
use Stasis\Router\Route\RouteInterface;

/**
 * @internal
 * @implements \IteratorAggregate<int, RouteInterface>
 */
class RouteSourceCollection implements \IteratorAggregate
{
    /** @var array<RouteSource> */
    private array $sources = [];

    public function add(RouteSource $source): void
    {
        $name = $source->name;
        if (isset($this->sources[$name])) {
            throw new LogicException(sprintf('Route source "%s" already exists.', $name));
        }

        $this->sources[$name] = $source;
    }

    /**
     * @return \Generator<int, RouteInterface>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->sources as $source) {
            foreach ($source->routes as $route) {
                yield $route;
            }
        }
    }
}
