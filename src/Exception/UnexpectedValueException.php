<?php

declare(strict_types=1);

namespace Laminas\Di\Exception;

use UnexpectedValueException as BaseUnexpectedValueException;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class UnexpectedValueException extends BaseUnexpectedValueException implements ExceptionInterface
{
}
