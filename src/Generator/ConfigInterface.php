<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator;

use Psr\Container\ContainerInterface;
use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Router\Route\RouteInterface;

interface ConfigInterface
{
    /** @return iterable<RouteInterface> */
    public function routes(): iterable;

    public function container(): ContainerInterface;

    public function distribution(): DistributionInterface;
}
