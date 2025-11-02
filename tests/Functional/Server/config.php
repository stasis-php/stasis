<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Stasis\Config\ConfigInterface;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\FilesystemDistribution;
use Stasis\ServiceLocator\NoContainer;

return new class implements ConfigInterface {
    public function routes(): iterable
    {
        return [];
    }

    public function container(): ContainerInterface
    {
        return new NoContainer();
    }

    public function distribution(): DistributionInterface
    {
        return new FilesystemDistribution(__DIR__ . '/fake_dist');
    }

    public function extensions(): iterable
    {
        return [];
    }
};
