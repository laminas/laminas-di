<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Di;

use Zend\Di\CodeGenerator\InjectorGenerator;
use Zend\Di\ConfigInterface as ZendDiConfigInterface;
use Zend\Di\InjectorInterface as ZendDiInjectorInterface;

/**
 * Implements the config provider for mezzio
 */
class ConfigProvider
{
    /**
     * Implements the config provider
     *
     * @return array The configuration for mezzio
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Returns the dependency (service manager) configuration
     */
    public function getDependencyConfig(): array
    {
        return [
            // Legacy Zend Framework aliases
            'aliases'            => [
                ZendDiInjectorInterface::class => InjectorInterface::class,
                ZendDiConfigInterface::class   => ConfigInterface::class,
                InjectorGenerator::class       => CodeGenerator\InjectorGenerator::class,
            ],
            'factories'          => [
                InjectorInterface::class               => Container\InjectorFactory::class,
                ConfigInterface::class                 => Container\ConfigFactory::class,
                CodeGenerator\InjectorGenerator::class => Container\GeneratorFactory::class,
            ],
            'abstract_factories' => [
                Container\ServiceManager\AutowireFactory::class,
            ],
        ];
    }
}
