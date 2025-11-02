<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\ServiceLocator;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Stasis\Exception\LogicException;

class FakeContainer implements ContainerInterface
{
    /**
     * @param array<string, mixed> $services
     */
    public function __construct(
        private array $services = [],
    ) {}

    public function get(string $id): mixed
    {
        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        throw new class ($id) extends LogicException implements NotFoundExceptionInterface {
            public function __construct(string $id)
            {
                parent::__construct(sprintf('Unable to get "%s" from the container.', $id));
            }
        };
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
