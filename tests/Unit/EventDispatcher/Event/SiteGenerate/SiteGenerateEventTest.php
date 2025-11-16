<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher\Event\SiteGenerate;

use Stasis\EventDispatcher\Event\SiteGenerate\SiteGenerateData;
use Stasis\EventDispatcher\Event\SiteGenerate\SiteGenerateEvent;
use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\Listener\SiteGenerateInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Router;

class SiteGenerateEventTest extends TestCase
{
    public function testAcceptMatchingListener(): void
    {
        $router = $this->createMock(Router::class);
        $event = new SiteGenerateEvent($router);
        $data = new SiteGenerateData($router);

        $listener = $this->createMock(SiteGenerateInterface::class);
        $listener
            ->expects($this->once())
            ->method('onSiteGenerate')
            ->with(self::equalTo($data));

        $isAccepted = $event->accept($listener);
        self::assertTrue($isAccepted, 'Event should be accepted by a matching listener.');
    }

    public function testAcceptNonMatchingListener(): void
    {
        $router = $this->createMock(Router::class);
        $event = new SiteGenerateEvent($router);

        $listener = $this->createMock(ListenerInterface::class);
        $isAccepted = $event->accept($listener);
        self::assertFalse($isAccepted, 'Event should not be accepted by a non-matching listener.');
    }
}
