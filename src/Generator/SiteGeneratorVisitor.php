<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator;

use Vstelmakh\Stasis\Controller\ControllerInterface;
use Vstelmakh\Stasis\Exception\RuntimeException;
use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Router\Compiler\RouteType\ControllerType;
use Vstelmakh\Stasis\Router\Compiler\RouteType\TypeVisitorInterface;
use Vstelmakh\Stasis\ServiceLocator\ServiceLocator;

class SiteGeneratorVisitor implements TypeVisitorInterface
{
    public function __construct(
        private readonly string $path,
        private readonly ServiceLocator $serviceLocator,
        private readonly DistributionInterface $distribution,
    ) {}

    public function visitController(ControllerType $controller): void
    {
        $service = $this->serviceLocator->get($controller->class, ControllerInterface::class);
        $content = $this->render($service, $controller->parameters);
        $path = $this->path . '/index.html';
        $this->distribution->write($path, $content);
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
