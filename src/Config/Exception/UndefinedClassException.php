<?php

declare(strict_types=1);

namespace Laminas\Di\Config\Exception;

use Throwable;

use function sprintf;

/**
 * This is thrown when a configured type does not exist
 */
class UndefinedClassException extends InvalidConfigException
{
    public function __construct(
        public readonly string $className,
        string|null $message = null,
        int $code = 0,
        Throwable|null $previous = null
    ) {
        parent::__construct($message ?? sprintf('Class "%s" does not exists', $className), $code, $previous);
    }
}
