<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Server;

use PHPUnit\Framework\TestCase;
use Stasis\Server\ServerFactory;

class ServerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new ServerFactory();
        $server = $factory->create(__DIR__, 'localhost', 8080);
        $this->expectNotToPerformAssertions();
    }
}
