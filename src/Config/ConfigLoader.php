<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Config;

use Symfony\Component\Filesystem\Path;
use Vstelmakh\Stasis\Exception\RuntimeException;

class ConfigLoader
{
    public const string DEFAULT_CONFIG = 'stasis.php';

    public function __construct(
        private readonly string $projectRoot,
        private readonly ?string $configPath = null,
    ) {}

    public function load(): ConfigInterface
    {
        $path = $this->configPath ?? self::DEFAULT_CONFIG;
        $configPath = Path::isAbsolute($path) ? $path : Path::canonicalize($this->projectRoot . '/' . $path);

        if (!is_file($configPath)) {
            throw new RuntimeException(sprintf('Config file "%s" does not exist.', $configPath));
        }

        $config = require $configPath;
        if (!$config instanceof ConfigInterface) {
            throw new RuntimeException(sprintf('Config "%s" does not implement "%s" interface.', $configPath, ConfigInterface::class));
        }

        return $config;
    }
}
