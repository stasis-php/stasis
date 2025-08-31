<?php

declare(strict_types=1);

namespace Stasis\Router\Route;

/**
 * Route provider is generating dynamic routes, for example, pagination.
 */
interface RouteProviderInterface
{
    /**
     * @return iterable<RouteInterface>
     */
    public function routes(): iterable;
}
