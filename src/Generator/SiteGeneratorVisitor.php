<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator;

use Vstelmakh\Stasis\Controller\ControllerInterface;
use Vstelmakh\Stasis\Exception\RuntimeException;
use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Router\Compiler\Resource\ControllerResource;
use Vstelmakh\Stasis\Router\Compiler\Resource\FileResource;
use Vstelmakh\Stasis\Router\Compiler\Resource\ResourceVisitorInterface;
use Vstelmakh\Stasis\Router\Router;
use Vstelmakh\Stasis\ServiceLocator\ServiceLocator;

class SiteGeneratorVisitor implements ResourceVisitorInterface
{
    public function __construct(
        private readonly string $path,
        private readonly ServiceLocator $serviceLocator,
        private readonly DistributionInterface $distribution,
        private readonly Router $router,
    ) {}

    public function visitController(ControllerResource $controller): void
    {
        $service = $this->serviceLocator->get($controller->class, ControllerInterface::class);
        $content = $this->render($service, $controller->parameters);
        $path = $this->path . '/index.html';
        $this->distribution->write($path, $content);
    }

    public function visitFile(FileResource $file): void
    {
        $this->distribution->copy($file->source, $this->path);
    }

    /**
     * @return string|resource
     */
    private function render(ControllerInterface $controller, $parameters = [])
    {
        $content = $controller->render($this->router, $parameters);

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
