<?php

declare(strict_types=1);

namespace Stasis\Router\Compiler\Resource;

interface ResourceVisitorInterface
{
    public function visitController(ControllerResource $resource): void;

    public function visitFile(FileResource $resource): void;
}
