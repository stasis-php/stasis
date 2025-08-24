<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Config;

use Psr\Container\ContainerInterface;
use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Router\Route\RouteInterface;

interface ConfigInterface
{
    /**
     * Defines the routes that will be generated.
     * @return iterable<RouteInterface>
     */
    public function routes(): iterable;

    /**
     * Defines the container used to resolve the services (e.g., controllers) that are required for website generation.
     */
    public function container(): ContainerInterface;

    /**
     * Defines the distribution where the generated files will be placed.
     */
    public function distribution(): DistributionInterface;
}
