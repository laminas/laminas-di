<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Di\Definition;

/**
 * Parameter definition
 */
interface ParameterInterface
{
    public function getName(): string;

    public function getPosition(): int;

    public function getType(): ?string;

    /**
     * @return mixed
     */
    public function getDefault();

    public function isRequired(): bool;

    public function isBuiltin(): bool;
}
