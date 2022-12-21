<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CodeGenerator;

use Laminas\Di\CodeGenerator\FactoryInterface;
use Psr\Container\ContainerInterface;
use stdClass;

/** @template-implements FactoryInterface<stdClass> */
class StdClassFactory implements FactoryInterface
{
    public function create(ContainerInterface $container, array $options)
    {
        return new stdClass();
    }
}
