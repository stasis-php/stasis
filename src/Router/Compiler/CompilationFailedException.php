<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Vstelmakh\Stasis\Exception\StasisExceptionInterface;

class CompilationFailedException extends \RuntimeException implements StasisExceptionInterface
{
    public function __construct(string $message, array $routeData = [], ?\Throwable $previous = null)
    {
        if (!empty($routeData)) {
            $routeDetails = json_encode(
                $routeData,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            );

            $message = sprintf('%s Route: %s.', $message, $routeDetails);
        }

        parent::__construct($message, 0 , $previous);
    }
}
