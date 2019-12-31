<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Container;

use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Di\ConfigInterface;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Resolver\DependencyResolver;
use Psr\Container\ContainerInterface;

class GeneratorFactory
{
    private function getConfig(ContainerInterface $container) : ConfigInterface
    {
        if ($container->has(ConfigInterface::class)) {
            return $container->get(ConfigInterface::class);
        }

        if ($container->has(\Zend\Di\ConfigInterface::class)) {
            return $container->get(\Zend\Di\ConfigInterface::class);
        }

        return (new ConfigFactory())->create($container);
    }

    public function create(ContainerInterface $container) : InjectorGenerator
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $diConfig = $this->getConfig($container);
        $aotConfig = $config['dependencies']['auto']['aot'] ?? [];
        $resolver = new DependencyResolver(new RuntimeDefinition(), $diConfig);
        $namespace = $aotConfig['namespace'] ?? null;

        $resolver->setContainer($container);
        $generator = new InjectorGenerator($diConfig, $resolver, $namespace);

        if (isset($aotConfig['directory'])) {
            $generator->setOutputDirectory($aotConfig['directory']);
        }

        return $generator;
    }

    public function __invoke(ContainerInterface $container) : InjectorGenerator
    {
        return $this->create($container);
    }
}
