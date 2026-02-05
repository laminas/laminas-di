<?php

declare(strict_types=1);

namespace Laminas\Di\Exception;

use DomainException;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class UndefinedReferenceException extends DomainException implements ExceptionInterface
{
}
