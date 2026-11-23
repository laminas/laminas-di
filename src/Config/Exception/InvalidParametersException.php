<?php

declare(strict_types=1);

namespace Laminas\Di\Config\Exception;

use function sprintf;

class InvalidParametersException extends InvalidConfigException
{
    public static function numericParamKey(int $key): self
    {
        return new self(
            sprintf(
                'Parameter name must be an identifier, got a numeric index %d',
                $key,
            ),
        );
    }
}
