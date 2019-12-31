<?php

/**
 * Generated factory for \LaminasTest\Di\TestAsset\Constructor\MixedArguments
 */

declare(strict_types=1);

namespace LaminasTest\Di\Generated\Factory\LaminasTest\Di\TestAsset\Constructor;

use Laminas\Di\CodeGenerator\FactoryInterface;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function is_array;

final class MixedArgumentsFactory implements FactoryInterface
{
    public function create(ContainerInterface $container, array $options = [])
    {
        $args = empty($options)
            ? [
                $container->get('LaminasTest\\Di\\TestAsset\\Constructor\\NoConstructor'), // objectDep
                null, // anyDep
            ]
            : [
                array_key_exists('objectDep', $options) ? $options['objectDep'] : $container->get('LaminasTest\\Di\\TestAsset\\Constructor\\NoConstructor'),
                array_key_exists('anyDep', $options) ? $options['anyDep'] : null,
            ];

        return new \LaminasTest\Di\TestAsset\Constructor\MixedArguments(...$args);
    }

    public function __invoke(ContainerInterface $container, $name = null, array $options = null)
    {
        if (is_array($name) && $options === null) {
            $options = $name;
        }

        return $this->create($container, $options ?? []);
    }
}
