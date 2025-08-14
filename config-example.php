<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Vstelmakh\Stasis\Generator\ConfigInterface;
use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Generator\Distribution\FilesystemDistribution;
use Vstelmakh\Stasis\Router\Route\Group;
use Vstelmakh\Stasis\Router\Route\Route;

return new class implements ConfigInterface {

    public function routes(): iterable
    {
        return [
            new Route(path: '/', controller: 'controller_id_in_container', name: 'homepage'),
            new Group(path: '/blog', routes: [
                new Route(path: '/article-1', controller: 'controller_id_in_container', parameters: ['id' => 1]),
                new Route(path: '/article-2', controller: 'controller_id_in_container', parameters: ['id' => 2]),
            ]),
            // TODO: add routes
        ];
    }

    public function container(): ContainerInterface
    {
        // TODO: return container instance
    }

    public function distribution(): DistributionInterface
    {
        return new FilesystemDistribution(__DIR__ . '/dist');
    }
};
