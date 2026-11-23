<?php

declare(strict_types=1);

namespace Laminas\Di\Config;

/**
 * Provides type configuration for a type alias (virtual type)
 */
final readonly class AliasConfig
{
    public function __construct(
        public string $name,
        public TypeConfig $type,
    ) {
    }
}
