<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Stasis\Config\ConfigInterface;
use Stasis\Config\ConfigLoader;
use Stasis\Exception\RuntimeException;

class ConfigLoaderTest extends TestCase
{
    public function testLoadDefault(): void
    {
        $loader = new ConfigLoader(__DIR__ . '/fake_configs');
        $config = $loader->load();
        self::assertInstanceOf(ConfigInterface::class, $config);
    }

    public function testLoadCustomRelativePath(): void
    {
        $loader = new ConfigLoader(__DIR__ . '/fake_configs', 'custom.php');
        $config = $loader->load();
        self::assertInstanceOf(ConfigInterface::class, $config);
    }

    public function testLoadCustomAbsolutePath(): void
    {
        $loader = new ConfigLoader(__DIR__ . '/fake_configs', __DIR__ . '/fake_configs/custom.php');
        $config = $loader->load();
        self::assertInstanceOf(ConfigInterface::class, $config);
    }

    public function testLoadMissing(): void
    {
        $loader = new ConfigLoader(__DIR__ . '/fake_configs', 'missing.php');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not exist');

        $loader->load();
    }

    public function testLoadInvalidType(): void
    {
        $loader = new ConfigLoader(__DIR__ . '/fake_configs', 'invalid.php');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not implement');

        $loader->load();
    }
}
