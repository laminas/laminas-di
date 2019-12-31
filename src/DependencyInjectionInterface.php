<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di;

interface DependencyInjectionInterface extends LocatorInterface
{
    /**
     * Retrieve a new instance of a class
     *
     * Forces retrieval of a discrete instance of the given class, using the
     * constructor parameters provided.
     *
     * @param  mixed       $name   Class name or service alias
     * @param  array       $params Parameters to pass to the constructor
     * @return object|null
     */
    public function newInstance($name, array $params = []);
}
