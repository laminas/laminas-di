<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Definition;

/**
 * Parameter definition
 */
interface ParameterInterface
{
    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return int
     */
    public function getPosition() : int;

    /**
     * @return string|null
     */
    public function getType() : ?string;

    /**
     * @return mixed
     */
    public function getDefault();

    /**
     * @return bool
     */
    public function isRequired() : bool;

    /**
     * @return bool
     */
    public function isBuiltin() : bool;
}
