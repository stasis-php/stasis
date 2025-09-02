<?php

declare(strict_types=1);

namespace Stasis\ServiceLocator;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Stasis\Config\ConfigInterface;
use Stasis\Exception\LogicException;

class NullContainer implements ContainerInterface
{
    public function get(string $id)
    {
        throw new class($id) extends LogicException implements NotFoundExceptionInterface {
            public function __construct(string $id) {
                parent::__construct(sprintf(
                    'Unable to get "%s" from the container.'
                    . ' No container configured in the config.'
                    . 'Consider configuring the container with %s::container() or use instances directly.',
                    $id,
                    ConfigInterface::class
                ));
            }
        };
    }

    public function has(string $id): bool
    {
        return false;
    }
}
