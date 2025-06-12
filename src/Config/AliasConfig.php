<?php

declare(strict_types=1);

namespace Laminas\Di\Config;

/**
 * Provides type configuration for a type alias (virtual type)
 *
 * @readonly
 */
final class AliasConfig
{
    public function __construct(
        public readonly string $name,
        public readonly TypeConfig $type,
    ) {
    }
}
