<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router;

/**
 * Definition of the endpoint which can be compiled by the router.
 */
interface RouteInterface
{
    public function accept(RouteVisitorInterface $visitor): void;
}
