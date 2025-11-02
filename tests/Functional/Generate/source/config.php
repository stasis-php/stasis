<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Stasis\Config\ConfigInterface;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\FilesystemDistribution;
use Stasis\Router\Route\Asset;
use Stasis\Router\Route\Group;
use Stasis\Router\Route\Route;
use Stasis\Tests\Doubles\ServiceLocator\FakeContainer;
use Stasis\Tests\Functional\Generate\source\CurrentTimeController;

return new class implements ConfigInterface {
    public function routes(): iterable
    {
        return [
            new Route('/', fn() => 'Hello World!', 'home'),
            new Route('/about.html', fn($router, $params) => sprintf('%s is cool!', $params['name']), 'about', ['name' => 'Stasis']),
            new Route('/time.html', CurrentTimeController::class, 'time', ['format' => 'Y-m-d H:i:s']),
            new Group('/blog', [
                new Route('/article1.html', fn() => 'Article 1'),
                new Route('/article2.html', fn() => 'Article 2'),
            ]),
            new Asset('/style.css', __DIR__ . '/style.css'),
            new Asset('/assets', __DIR__ . '/assets'),
        ];
    }

    public function container(): ContainerInterface
    {
        return new FakeContainer([
            CurrentTimeController::class => new CurrentTimeController(),
        ]);
    }

    public function distribution(): DistributionInterface
    {
        return new FilesystemDistribution(__DIR__ . '/../dist');
    }

    public function extensions(): iterable
    {
        return [];
    }
};
