<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Exception;

use DomainException;
use Throwable;

class ClassNotFoundException extends DomainException implements ExceptionInterface
{
    public function __construct(string $classname, ?int $code = null, ?Throwable $previous = null)
    {
        parent::__construct("The class '$classname' does not exist.", $code ?? 0, $previous);
    }
}
