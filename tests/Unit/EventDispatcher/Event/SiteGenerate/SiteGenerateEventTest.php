<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher\Event\SiteGenerate;

use Stasis\EventDispatcher\Event\SiteGenerate\SiteGenerateData;
use Stasis\EventDispatcher\Event\SiteGenerate\SiteGenerateEvent;
use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\Listener\SiteGenerateInterface;
use Stasis\Router\Router;
use Stasis\Tests\Unit\EventDispatcher\Event\EventTestCase;

class SiteGenerateEventTest extends EventTestCase
{
    private Router $router;

    public function setUp(): void
    {
        parent::setUp();
        $this->router = $this->createMock(Router::class);
    }

    protected function getEvent(): EventInterface
    {
        return new SiteGenerateEvent($this->router);
    }

    protected function getEventData(): mixed
    {
        return new SiteGenerateData($this->router);
    }

    protected function getListenerClass(): string
    {
        return SiteGenerateInterface::class;
    }

    protected function getListenerMethod(): string
    {
        return 'onSiteGenerate';
    }
}
