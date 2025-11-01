<?php

declare(strict_types=1);

namespace Stasis\Tests\Server;

use Stasis\Server\ServerFactory;
use PHPUnit\Framework\TestCase;

class ServerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new ServerFactory();
        $server = $factory->create(__DIR__, 'localhost', 8080);
        $this->expectNotToPerformAssertions();
    }
}
