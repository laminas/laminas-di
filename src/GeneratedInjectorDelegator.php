<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di;

use Laminas\Di\Exception\InvalidServiceConfigException;
use Psr\Container\ContainerInterface;

use function class_exists;
use function is_string;

class GeneratedInjectorDelegator
{
    /**
     * @param string $name
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback): InjectorInterface
    {
        $config    = $container->has('config') ? $container->get('config') : [];
        $aotConfig = $config['dependencies']['auto']['aot'] ?? [];
        $namespace = ! isset($aotConfig['namespace']) || $aotConfig['namespace'] === ''
            ? 'Laminas\Di\Generated'
            : $aotConfig['namespace'];

        if (! is_string($namespace)) {
            throw new InvalidServiceConfigException('Provided namespace is not a string.');
        }

        $injector = $callback();

        $generatedInjector = $namespace . '\\GeneratedInjector';
        if (class_exists($generatedInjector)) {
            return new $generatedInjector($injector);
        }

        return $injector;
    }
}
