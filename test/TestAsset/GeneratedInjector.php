<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset;

use Laminas\Di\InjectorInterface;

class GeneratedInjector implements InjectorInterface
{
    /** @var InjectorInterface */
    private $injector;

    public function __construct(InjectorInterface $injector)
    {
        $this->injector = $injector;
    }

    public function getInjector() : InjectorInterface
    {
        return $this->injector;
    }

    public function canCreate(string $name) : bool
    {
        return $this->injector->canCreate($name);
    }

    public function create(string $name, array $options = [])
    {
        return $this->injector->create($name, $options);
    }
}
