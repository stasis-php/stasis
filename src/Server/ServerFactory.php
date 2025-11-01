<?php

declare(strict_types=1);

namespace Stasis\Server;

class ServerFactory
{
    public function create(string $path, string $host, int $port): Server
    {
        return new Server($path, $host, $port);
    }
}
