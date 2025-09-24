<?php

declare(strict_types=1);

namespace Stasis;

use Psr\Container\ContainerInterface;
use Stasis\Config\ConfigInterface;
use Stasis\Config\ConfigLoader;
use Stasis\EventDispatcher\Event\RouteConfigure\RouteConfigureEvent;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\Extension\ExtensionLoader;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Router\Route\RouteInterface;
use Stasis\Router\Source\RouteSource;
use Stasis\Router\Source\RouteSourceCollection;
use Stasis\Stopwatch\Stopwatch;

/**
 * @internal
 */
class Kernel
{
    /** @var iterable<RouteInterface> */
    private iterable $routes;
    private ConfigInterface $config;
    private ContainerInterface $container;
    private DistributionInterface $distribution;
    private bool $isExtensionsLoaded = false;

    public static function create(string $projectRoot, ?string $configPath = null): self
    {
        $configLoader = new ConfigLoader($projectRoot, $configPath);
        $eventDispatcher = new EventDispatcher();
        $extensionLoader = new ExtensionLoader($eventDispatcher);
        $stopwatch = new Stopwatch();
        return new self($configLoader, $eventDispatcher, $extensionLoader, $stopwatch);
    }

    public function __construct(
        private readonly ConfigLoader $configLoader,
        private readonly EventDispatcher $eventDispatcher,
        private readonly ExtensionLoader $extensionLoader,
        private readonly Stopwatch $stopwatch,
    ) {}

    /**
     * @return iterable<RouteInterface>
     */
    public function routes(): iterable
    {
        if (!isset($this->routes)) {
            $this->routes = new RouteSourceCollection();

            $configRoutes = $this->getConfig()->routes();
            $this->routes->add(new RouteSource('config', $configRoutes));

            $event = new RouteConfigureEvent($this->routes);
            $this->event()->dispatch($event);
        }

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

    public function event(): EventDispatcher
    {
        if (!$this->isExtensionsLoaded) {
            $extensions = $this->getConfig()->extensions();
            $this->extensionLoader->load($extensions);
            $this->isExtensionsLoaded = true;
        }

        return $this->eventDispatcher;
    }

    public function stopwatch(): Stopwatch
    {
        return $this->stopwatch;
    }

    private function getConfig(): ConfigInterface
    {
        $this->config = $this->config ?? $this->configLoader->load();
        return $this->config;
    }
}
