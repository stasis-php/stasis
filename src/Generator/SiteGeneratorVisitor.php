<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Vstelmakh\Stasis\Controller\ControllerInterface;
use Vstelmakh\Stasis\Exception\RuntimeException;
use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Router\Compiler\RouteType\ControllerType;
use Vstelmakh\Stasis\Router\Compiler\RouteType\TypeVisitorInterface;

class SiteGeneratorVisitor implements TypeVisitorInterface
{
    public function __construct(
        private readonly string $path,
        private readonly ContainerInterface $container,
        private readonly DistributionInterface $distribution,
    ) {}

    public function visitController(ControllerType $controller): void
    {
        $class = $controller->class;
        $object = $this->getController($class);
        $content = $this->render($object, $controller->parameters);
        $path = $this->path . '/index.html';
        $this->distribution->write($path, $content);
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
                'Invalid controller received from container. "%s" does not implement "%s".',
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
                'Unexpected return type "%s" of %s::render(). Expected string or resource.',
                gettype($content),
                $controller::class
            ));
        }

        return $content;
    }
}
