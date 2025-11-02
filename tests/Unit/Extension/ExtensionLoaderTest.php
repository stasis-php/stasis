<?php

declare(strict_types=1);

namespace Stasis\Tests\Extension;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Exception\LogicException;
use Stasis\Extension\ExtensionInterface;
use Stasis\Extension\ExtensionLoader;

class ExtensionLoaderTest extends TestCase
{
    private MockObject&EventDispatcher $dispatcher;
    private ExtensionLoader $loader;

    public function setUp(): void
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->loader = new ExtensionLoader($this->dispatcher);
    }

    public function testLoad(): void
    {
        $listenerA = new class implements ListenerInterface {};
        $listenerB = new class implements ListenerInterface {};
        $listenerC = new class implements ListenerInterface {};

        $extension1 = $this->createMock(ExtensionInterface::class);
        $extension1->method('listeners')->willReturn([$listenerA, $listenerB]);

        $extension2 = $this->createMock(ExtensionInterface::class);
        $extension2->method('listeners')->willReturn([$listenerC]);

        $listeners = [];
        $this->dispatcher
            ->expects($this->exactly(3))
            ->method('add')
            ->willReturnCallback(static function (ListenerInterface $listener) use (&$listeners): void {
                $listeners[] = $listener;
            });

        $this->loader->load([$extension1, $extension2]);

        self::assertContains($listenerA, $listeners, 'Missing listener A.');
        self::assertContains($listenerB, $listeners, 'Missing listener B.');
        self::assertContains($listenerC, $listeners, 'Missing listener C.');
        self::assertCount(3, $listeners, 'Unexpected count of listeners.');
    }

    public function testLoadInvalidExtensionType(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unexpected extension of type');

        $this->loader->load([new \stdClass()]); // @phpstan-ignore-line
    }

    public function testLoadTInvalidListenerType(): void
    {
        $badListener = new \stdClass();
        $extension = $this->createMock(ExtensionInterface::class);
        $extension->method('listeners')->willReturn([$badListener]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unexpected listener of type');

        $this->loader->load([$extension]);
    }
}
