<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Exception;

use DomainException;

class ClassNotFoundException extends DomainException implements ExceptionInterface
{
    /**
     * @param   string          $classname
     * @param   int             $code
     * @param   \Throwable|null $previous
     */
    public function __construct($classname, $code = null, $previous = null)
    {
        parent::__construct("The class '$classname' does not exist.", $code, $previous);
    }
}
