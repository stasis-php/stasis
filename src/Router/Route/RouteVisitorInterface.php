<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Route;

interface RouteVisitorInterface
{
    public function visitRoute(Route $route): void;

    public function visitGroup(Group $group): void;

    public function visitAsset(Asset $asset): void;
}
