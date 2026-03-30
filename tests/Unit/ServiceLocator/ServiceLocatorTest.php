<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\ServiceLocator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Stasis\Exception\LogicException;
use Stasis\ServiceLocator\ServiceLocator;
use Stasis\Tests\Doubles\ServiceLocator\NotFoundException;

class ServiceLocatorTest extends TestCase
{
    private MockObject&ContainerInterface $container;
    private ServiceLocator $serviceLocator;

    #[\Override]
    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->serviceLocator = new ServiceLocator($this->container);
    }

    public function testGet(): void
    {
        $reference = 'service_id';
        $service = new \stdClass();

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with($reference)
            ->willReturn($service);

        $actual = $this->serviceLocator->get($reference, \stdClass::class);
        self::assertSame($service, $actual);
    }

    public function testGetThrowsNotFound(): void
    {
        $reference = 'service_id';
        $service = new \stdClass();

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with($reference)
            ->willThrowException(new NotFoundException(sprintf('Service "%s" not found.', $reference)));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Error on get service "service_id" from container.');

        $this->serviceLocator->get($reference, \stdClass::class);
    }

    public function testGetThrowsOnUnexpectedType(): void
    {
        $reference = 'service_id';
        $service = new \stdClass();

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with($reference)
            ->willReturn($service);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unexpected service "service_id" type received from container. "stdClass" does not implement "DateTimeImmutable".');
        $this->serviceLocator->get($reference, \DateTimeImmutable::class);
    }
}
