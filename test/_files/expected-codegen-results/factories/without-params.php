<?php

/**
 * Generated factory for \LaminasTest\Di\TestAsset\A
 */

namespace LaminasTest\Di\Generated\Factory\LaminasTest\Di\TestAsset;

use Laminas\Di\CodeGenerator\FactoryInterface;
use Psr\Container\ContainerInterface;

use function is_array;

final class AFactory implements FactoryInterface
{
    public function create(ContainerInterface $container, array $options = [])
    {
        return new \LaminasTest\Di\TestAsset\A();
    }

    public function __invoke(ContainerInterface $container, $name = null, array $options = null)
    {
        if (is_array($name) && $options === null) {
            $options = $name;
        }

        return $this->create($container, $options ?? []);
    }
}
