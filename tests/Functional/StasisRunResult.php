<?php

declare(strict_types=1);

namespace Stasis\Tests\Functional;

readonly class StasisRunResult
{
    public function __construct(
        public string $command,
        public string $output,
        public int $exitCode,
    ) {}
}
