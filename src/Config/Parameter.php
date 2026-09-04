<?php

declare(strict_types=1);

namespace Laminas\Di\Config;

use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Resolver\ValueInjection;

use function is_string;

/**
 * Parameter injection configuration
 */
final readonly class Parameter
{
    public function __construct(
        public string $name,
        public string|InjectionInterface $injection,
    ) {
    }

    public static function fromValue(string $name, mixed $value): self
    {
        return new self(
            $name,
            is_string($value) || $value instanceof InjectionInterface
                ? $value
                : new ValueInjection($value),
        );
    }
}
