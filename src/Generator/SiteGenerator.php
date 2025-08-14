<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Vstelmakh\Stasis\Controller\ControllerInterface;
use Vstelmakh\Stasis\Exception\RuntimeException;
use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Router\Router;

class SiteGenerator
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly DistributionInterface $distribution,
    ) {}

    public function generate(Router $router): void
    {
        $this->clearDist();

        $routes = $router->all();
        foreach ($routes as $route) {
            $controllerClass = $route->controller;
            $controller = $this->getController($controllerClass);
            $content = $this->render($controller, $route);

            $this->writeDist($route->distPath, $content);
        }
    }

    private function clearDist(): void
    {
        try {
            $this->distribution->clear();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to clear dist.', $exception->getCode(), $exception);
        }
    }

    /**
     * @param class-string<ControllerInterface> $class
     */
    private function getController(string $class): ControllerInterface
    {
        try {
            $controller = $this->container->get($class);
        } catch (ContainerExceptionInterface $exception) {
            $message = sprintf('Error getting controller "%s" from container.', $class);
            throw new RuntimeException($message, $exception->getCode(), $exception);
        }

        if (!$controller instanceof ControllerInterface) {
            throw new RuntimeException(sprintf(
                'Entry with id "%s", received from container, is not implementing "%s".',
                $class,
                ControllerInterface::class
            ));
        }

        return $controller;
    }

    /**
     * @return string|resource
     */
    private function render(ControllerInterface $controller, $parameters = [])
    {
        $content = $controller->render($parameters);

        if (!is_string($content) && !is_resource($content)) {
            throw new RuntimeException(sprintf(
                'Unexpected return type "%s" of %s::render().',
                gettype($content),
                $controller::class
            ));
        }

        return $content;
    }

    private function writeDist(string $path, $content): void
    {
        try {
            $this->distribution->write($path, $content);
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                sprintf('Unable to write "%s" to dist.', $path),
                $exception->getCode(),
                $exception
            );
        }
    }
}
