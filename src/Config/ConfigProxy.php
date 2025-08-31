<?php

declare(strict_types=1);

namespace Stasis\Config;

use Psr\Container\ContainerInterface;
use Stasis\Generator\Distribution\DistributionInterface;

/**
 * This proxy delays loading of config until it is used, as well as caching config values to avoid recalculations.
 */
class ConfigProxy implements ConfigInterface
{
    private iterable $routes;
    private ConfigInterface $config;
    private ContainerInterface $container;
    private DistributionInterface $distribution;

    public function __construct(
        private readonly ConfigLoader $loader,
    ) {}

    public function routes(): iterable
    {
        $this->routes = $this->routes ?? $this->getConfig()->routes();
        return $this->routes;
    }

    public function container(): ContainerInterface
    {
        $this->container = $this->container ?? $this->getConfig()->container();
        return $this->container;
    }

    public function distribution(): DistributionInterface
    {
        $this->distribution = $this->distribution ?? $this->getConfig()->distribution();
        return $this->distribution;
    }

    private function getConfig(): ConfigInterface
    {
        $this->config = $this->config ?? $this->loader->load();
        return $this->config;
    }
}
