<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CodeGenerator;

use Laminas\Di\CodeGenerator\FactoryInterface;
use Psr\Container\ContainerInterface;
use stdClass;

class StdClassFactory implements FactoryInterface
{
    public function create(ContainerInterface $container, array $options)
    {
        return new stdClass();
    }
}
