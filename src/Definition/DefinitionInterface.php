<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Di\Definition;

use Laminas\Di\Exception\ClassNotFoundException;

/**
 * Interface for class definitions
 */
interface DefinitionInterface
{
    /**
     * All class names in this definition
     *
     * @return string[]
     */
    public function getClasses(): array;

    /**
     * Whether a class exists in this definition
     */
    public function hasClass(string $class): bool;

    /**
     * @throws ClassNotFoundException
     */
    public function getClassDefinition(string $class): ClassDefinitionInterface;
}
