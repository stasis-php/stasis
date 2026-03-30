<?php

declare(strict_types=1);

namespace Stasis\Config;

use Psr\Container\ContainerInterface;
use Stasis\Extension\ExtensionInterface;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Router\Route\RouteInterface;

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

    /**
     * Defines the list of extensions to be used by the application. The extensions can be either instances of
     * {@see ExtensionInterface} or container references of the services that implement the interface.
     * @return iterable<ExtensionInterface|string>
     */
    public function extensions(): iterable;
}
