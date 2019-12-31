<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\CodeGenerator;

use Laminas\Di\DefaultContainer;
use Laminas\Di\InjectorInterface;
use Psr\Container\ContainerInterface;

use function is_string;

/**
 * Abstract class for code generated dependency injectors
 */
abstract class AbstractInjector implements InjectorInterface
{
    /**
     * @var string|FactoryInterface[]
     */
    protected $factories = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var InjectorInterface
     */
    private $injector;

    /**
     * {@inheritDoc}
     */
    public function __construct(InjectorInterface $injector, ContainerInterface $container = null)
    {
        $this->injector = $injector;
        $this->container = $container ?: new DefaultContainer($this);

        $this->loadFactoryList();
    }

    /**
     * Init factory list
     */
    abstract protected function loadFactoryList() : void;

    private function getFactory($type) : FactoryInterface
    {
        if (is_string($this->factories[$type])) {
            $factory = $this->factories[$type];
            $this->factories[$type] = new $factory();
        }

        return $this->factories[$type];
    }

    public function canCreate(string $name) : bool
    {
        return (isset($this->factories[$name]) || $this->injector->canCreate($name));
    }

    public function create(string $name, array $options = [])
    {
        if (isset($this->factories[$name])) {
            return $this->getFactory($name)->create($this->container, $options);
        }

        return $this->injector->create($name, $options);
    }
}
