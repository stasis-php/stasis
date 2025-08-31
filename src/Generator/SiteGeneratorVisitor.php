<?php

declare(strict_types=1);

namespace Stasis\Generator;

use Stasis\Controller\ControllerInterface;
use Stasis\Exception\LogicException;
use Stasis\Exception\RuntimeException;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\LocalDistributionInterface;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Compiler\Resource\ResourceVisitorInterface;
use Stasis\Router\Router;
use Stasis\ServiceLocator\ServiceLocator;

class SiteGeneratorVisitor implements ResourceVisitorInterface
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
        private readonly DistributionInterface $distribution,
        private readonly Router $router,
        private readonly string $path,
        private readonly bool $symlinkFiles,
    ) {
        if ($this->symlinkFiles && !$this->distribution instanceof LocalDistributionInterface) {
            throw new LogicException('Provided distribution does not support symlinks.');
        }
    }

    public function visitController(ControllerResource $controller): void
    {
        $service = $this->serviceLocator->get($controller->class, ControllerInterface::class);
        $content = $this->render($service, $controller->parameters);
        $path = $this->path;
        $this->distribution->write($path, $content);
    }

    public function visitFile(FileResource $file): void
    {
        if ($this->symlinkFiles) {
            $this->distribution->link($file->source, $this->path);
        } else {
            $this->distribution->copy($file->source, $this->path);
        }
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
