<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Di\CodeGenerator;

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    /**
     * Create an instance
     *
     * @return object
     */
    public function create(ContainerInterface $container, array $options);
}
