<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Definition;

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
    public function getClasses() : array;

    /**
     * Whether a class exists in this definition
     *
     * @param  string $class
     * @return bool
     */
    public function hasClass(string $class) : bool;

    /**
     * @param string $class
     * @throws \Laminas\Di\Exception\ClassNotFoundException
     * @return ClassDefinitionInterface
     */
    public function getClassDefinition(string $class) : ClassDefinitionInterface;
}
