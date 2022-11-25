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

/**
 * @template-implements FactoryInterface<\LaminasTest\Di\TestAsset\Constructor\MixedArguments>
 */
final class MixedArgumentsFactory implements FactoryInterface
{
    public function create(ContainerInterface $container, array $options = []): \LaminasTest\Di\TestAsset\Constructor\MixedArguments
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

        /** @psalm-suppress MixedArgument */
        return new \LaminasTest\Di\TestAsset\Constructor\MixedArguments(...$args);
    }

    /**
     * @param array<mixed>|string|null $name
     * @param array<mixed>|null $options
     */
    public function __invoke(ContainerInterface $container, $name = null, array $options = null): \LaminasTest\Di\TestAsset\Constructor\MixedArguments
    {
        if (is_array($name) && $options === null) {
            $options = $name;
        }

        return $this->create($container, $options ?? []);
    }
}
