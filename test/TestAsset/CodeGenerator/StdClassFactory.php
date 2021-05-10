<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CodeGenerator;

use Laminas\Di\CodeGenerator\FactoryInterface;
use Psr\Container\ContainerInterface;
use stdClass;

class StdClassFactory implements FactoryInterface
{
    /** @return stdClass */
    public function create(ContainerInterface $container, array $options)
    {
        return new stdClass();
    }
}
