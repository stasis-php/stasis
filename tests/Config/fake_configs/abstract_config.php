<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Stasis\Config\ConfigInterface;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\ServiceLocator\NoContainer;
use Stasis\Tests\Doubles\Generator\MockDistribution;

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
        return new MockDistribution();
    }

    public function extensions(): iterable
    {
        return [];
    }
};
