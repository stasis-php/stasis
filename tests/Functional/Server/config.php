<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Stasis\Config\ConfigInterface;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\FilesystemDistribution;
use Stasis\ServiceLocator\NoContainer;

return new class implements ConfigInterface {
    #[\Override]
    public function routes(): iterable
    {
        return [];
    }

    #[\Override]
    public function container(): ContainerInterface
    {
        return new NoContainer();
    }

    #[\Override]
    public function distribution(): DistributionInterface
    {
        return new FilesystemDistribution(__DIR__ . '/fake_dist');
    }

    #[\Override]
    public function extensions(): iterable
    {
        return [];
    }
};
