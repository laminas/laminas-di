<?php

/**
 * Generated factory for \LaminasTest\Di\TestAsset\A
 */

declare(strict_types=1);

namespace LaminasTest\Di\Generated\Factory\LaminasTest\Di\TestAsset;

use Laminas\Di\CodeGenerator\FactoryInterface;
use Psr\Container\ContainerInterface;

use function is_array;

/**
 * @template-implements FactoryInterface<\LaminasTest\Di\TestAsset\A>
 */
final class AFactory implements FactoryInterface
{
    public function create(ContainerInterface $container, array $options = []): \LaminasTest\Di\TestAsset\A
    {
        return new \LaminasTest\Di\TestAsset\A();
    }

    /**
     * @param array<mixed>|string|null $name
     * @param array<mixed>|null $options
     */
    public function __invoke(ContainerInterface $container, $name = null, array $options = null): \LaminasTest\Di\TestAsset\A
    {
        if (is_array($name) && $options === null) {
            $options = $name;
        }

        return $this->create($container, $options ?? []);
    }
}
