<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di;

interface ServiceLocatorInterface extends LocatorInterface
{
    /**
     * Register a service with the locator
     *
     * @abstract
     * @param  string                  $name
     * @param  mixed                   $service
     * @return ServiceLocatorInterface
     */
    public function set($name, $service);
}
