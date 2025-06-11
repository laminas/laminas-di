<?php

declare(strict_types=1);

namespace Laminas\Di\CodeGenerator;

use Psr\Container\ContainerInterface;

/**
 * @deprecated Since 3.16.0, the code generator will be replaced by a separate package in version 4.0
 *
 * @template T extends object
 */
interface FactoryInterface
{
    /**
     * Create an instance
     *
     * @param array<mixed> $options
     * @return T
     */
    public function create(ContainerInterface $container, array $options);
}
