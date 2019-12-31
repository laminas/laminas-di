<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di;

interface LocatorInterface
{
    /**
     * Retrieve a class instance
     *
     * @param  string      $name   Class name or service name
     * @param  null|array  $params Parameters to be used when instantiating a new instance of $name
     * @return object|null
     */
    public function get($name, array $params = array());
}
