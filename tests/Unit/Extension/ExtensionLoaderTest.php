<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Extension;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Exception\LogicException;
use Stasis\Extension\ExtensionInterface;
use Stasis\Extension\ExtensionLoader;
use Stasis\ServiceLocator\ServiceLocator;

#[AllowMockObjectsWithoutExpectations]
class ExtensionLoaderTest extends TestCase
{
    private MockObject&EventDispatcher $dispatcher;
    private MockObject&ServiceLocator $serviceLocator;
    private ExtensionLoader $loader;

    #[\Override]
    public function setUp(): void
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->serviceLocator = $this->createMock(ServiceLocator::class);
        $this->loader = new ExtensionLoader($this->dispatcher);
    }

    public function testLoad(): void
    {
        $listenerA = new class implements ListenerInterface {};
        $listenerB = new class implements ListenerInterface {};
        $listenerC = new class implements ListenerInterface {};

        $extension1 = self::createStub(ExtensionInterface::class);
        $extension1->method('listeners')->willReturn([$listenerA, $listenerB]);

        $extension2 = self::createStub(ExtensionInterface::class);
        $extension2->method('listeners')->willReturn([$listenerC]);

        $listeners = [];
        $this->dispatcher
            ->expects($this->exactly(3))
            ->method('add')
            ->willReturnCallback(static function (ListenerInterface $listener) use (&$listeners): void {
                $listeners[] = $listener;
            });

        $this->loader->load([$extension1, $extension2], $this->serviceLocator);

        self::assertContains($listenerA, $listeners, 'Missing listener A.');
        self::assertContains($listenerB, $listeners, 'Missing listener B.');
        self::assertContains($listenerC, $listeners, 'Missing listener C.');
        self::assertCount(3, $listeners, 'Unexpected count of listeners.');
    }

    public function testLoadInvalidExtensionType(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid extension type "stdClass".');
        $this->loader->load([new \stdClass()], $this->serviceLocator); // @phpstan-ignore-line
    }

    public function testLoadInvalidListenerType(): void
    {
        $badListener = new \stdClass();
        $extension = self::createStub(ExtensionInterface::class);
        $extension->method('listeners')->willReturn([$badListener]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unexpected listener of type');

        $this->loader->load([$extension], $this->serviceLocator);
    }

    public function testLoadExtensionFromContainer(): void
    {
        $extensionId = 'extension.reference';
        $listener = new class implements ListenerInterface {};

        $extension = self::createStub(ExtensionInterface::class);
        $extension->method('listeners')->willReturn([$listener]);

        $this->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($extensionId, ExtensionInterface::class)
            ->willReturn($extension);

        $this->dispatcher
            ->expects($this->once())
            ->method('add')
            ->with($listener);

        $this->loader->load([$extensionId], $this->serviceLocator);
    }

    public function testLoadExtensionFromContainerFailure(): void
    {
        $extensionId = 'extension.reference';

        $this->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($extensionId, ExtensionInterface::class)
            ->willThrowException(new \RuntimeException('Service not found.'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Failed to load extension with container reference "extension.reference".');

        $this->loader->load([$extensionId], $this->serviceLocator);
    }
}
