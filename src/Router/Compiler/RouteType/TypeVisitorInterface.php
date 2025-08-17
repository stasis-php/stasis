<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler\RouteType;

interface TypeVisitorInterface
{
    public function visitController(ControllerType $controller): void;

    public function visitFile(FileType $file): void;
}
