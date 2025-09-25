<?php

declare(strict_types=1);

namespace Stasis\Tests\ServiceLocator;

use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Stasis\ServiceLocator\NoContainer;

class NoContainerTest extends TestCase
{
    public function testHasAlwaysFalse(): void
    {
        $container = new NoContainer();
        self::assertFalse($container->has('any'));
    }

    public function testGetThrowsLogicAndNotFoundExceptionWithHelpfulMessage(): void
    {
        $container = new NoContainer();
        $this->expectException(NotFoundExceptionInterface::class);
        $this->expectExceptionMessageMatches('/Unable to get "any" from the container. No container configured in the config./');
        $container->get('any');
    }
}
