<?php

declare(strict_types=1);

namespace Stasis\Generator;

use Stasis\Controller\ControllerInterface;
use Stasis\Exception\LogicException;
use Stasis\Exception\RuntimeException;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\SymlinkDistributionInterface;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Compiler\Resource\ResourceVisitorInterface;
use Stasis\Router\Router;
use Stasis\ServiceLocator\ServiceLocator;

/**
 * @internal
 */
class SiteGeneratorVisitor implements ResourceVisitorInterface
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
        private readonly DistributionInterface $distribution,
        private readonly Router $router,
        private readonly string $path,
        private readonly bool $symlinkFiles,
    ) {
        if ($this->symlinkFiles && !$this->distribution instanceof SymlinkDistributionInterface) {
            throw new LogicException('Provided distribution does not support symlinks.');
        }
    }

    public function visitController(ControllerResource $resource): void
    {
        $controller = $this->getController($resource->reference);
        $content = $this->render($controller, $resource->parameters);
        $path = $this->path;
        $this->distribution->write($path, $content);
    }

    public function visitFile(FileResource $resource): void
    {
        if ($this->symlinkFiles) {
            // @phpstan-ignore-next-line Validated in constructor.
            $this->distribution->link($resource->source, $this->path);
        } else {
            $this->distribution->copy($resource->source, $this->path);
        }
    }

    private function getController(ControllerInterface|string|\Closure $reference): ControllerInterface
    {
        if ($reference instanceof ControllerInterface) {
            return $reference;
        }

        if (is_string($reference)) {
            return $this->serviceLocator->get($reference, ControllerInterface::class);
        }

        if ($reference instanceof \Closure) {
            return new class ($reference) implements ControllerInterface {
                public function __construct(private \Closure $closure) {}

                public function render(Router $router, array $parameters)
                {
                    return ($this->closure)($router, $parameters);
                }
            };
        }

        // This is a fallback to have a proper error message if this code is ever reached.
        // @phpstan-ignore-next-line
        throw new LogicException(sprintf(
            'Unexpected reference type "%s". Expected container reference, instance of %s or Closure".',
            get_debug_type($reference),
            ControllerInterface::class,
        ));
    }

    /**
     * @param array<string, mixed> $parameters
     * @return string|resource
     */
    private function render(ControllerInterface $controller, array $parameters = [])
    {
        $content = $controller->render($this->router, $parameters);

        if (!is_string($content) && !is_resource($content)) {
            throw new RuntimeException(sprintf(
                'Unexpected return type "%s" of %s::render(). Expected string or resource.',
                gettype($content),
                $controller::class,
            ));
        }

        return $content;
    }
}
