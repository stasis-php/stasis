<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\ServiceLocator;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \LogicException implements NotFoundExceptionInterface {}
