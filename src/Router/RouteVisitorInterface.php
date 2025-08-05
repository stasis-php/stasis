<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router;

interface RouteVisitorInterface
{
    public function visitRoute(Route $route): void;

    public function visitGroup(Group $group): void;
}
