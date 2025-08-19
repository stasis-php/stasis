<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler\Resource;

interface ResourceVisitorInterface
{
    public function visitController(ControllerResource $controller): void;

    public function visitFile(FileResource $file): void;
}
