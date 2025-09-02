<?php

declare(strict_types=1);

namespace Stasis\ServiceLocator;

use Psr\Container\ContainerInterface;
use Stasis\Exception\LogicException;

class ServiceLocator
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {}

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function get(string $id, string $class): object
    {
        try {
            $service = $this->container->get($id);
        } catch (\Throwable $exception) {
            $message = sprintf('Error on get service "%s" from container.', $class);
            throw new LogicException(message: $message, previous: $exception);
        }

        if (!$service instanceof $class) {
            $type = get_debug_type($service);

            throw new LogicException(sprintf(
                'Unexpected service "%s" type received from container. "%s" does not implement "%s".',
                $id,
                $type,
                $class,
            ));
        }

        return $service;
    }
}
