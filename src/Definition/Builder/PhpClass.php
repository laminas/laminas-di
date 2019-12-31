<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Definition\Builder;

/**
 * Object containing definitions for a single class
 */
class PhpClass
{
    /**
     * @var string
     */
    protected $defaultMethodBuilder = 'Laminas\Di\Definition\Builder\InjectionMethod';

    /**
     * @var null|string
     */
    protected $name                 = null;

    /**
     * @var string|\Callable|array
     */
    protected $instantiator         = '__construct';

    /**
     * @var InjectionMethod[]
     */
    protected $injectionMethods     = [];

    /**
     * @var array
     */
    protected $superTypes           = [];

    /**
     * Set name
     *
     * @param  string   $name
     * @return PhpClass
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string|\Callable|array $instantiator
     * @return PhpClass
     */
    public function setInstantiator($instantiator)
    {
        $this->instantiator = $instantiator;

        return $this;
    }

    /**
     * @return array|\Callable|string
     */
    public function getInstantiator()
    {
        return $this->instantiator;
    }

    /**
     * @param  string   $superType
     * @return PhpClass
     */
    public function addSuperType($superType)
    {
        $this->superTypes[] = $superType;

        return $this;
    }

    /**
     * Get super types
     *
     * @return array
     */
    public function getSuperTypes()
    {
        return $this->superTypes;
    }

    /**
     * Add injection method
     *
     * @param  InjectionMethod $injectionMethod
     * @return PhpClass
     */
    public function addInjectionMethod(InjectionMethod $injectionMethod)
    {
        $this->injectionMethods[] = $injectionMethod;

        return $this;
    }

    /**
     * Create and register an injection method
     *
     * Optionally takes the method name.
     *
     * This method may be used in lieu of addInjectionMethod() in
     * order to provide a more fluent interface for building classes with
     * injection methods.
     *
     * @param  null|string     $name
     * @return InjectionMethod
     */
    public function createInjectionMethod($name = null)
    {
        $builder = $this->defaultMethodBuilder;
        /* @var $method InjectionMethod */
        $method  = new $builder();
        if (null !== $name) {
            $method->setName($name);
        }
        $this->addInjectionMethod($method);

        return $method;
    }

    /**
     * Override which class will be used by {@link createInjectionMethod()}
     *
     * @param  string   $class
     * @return PhpClass
     */
    public function setMethodBuilder($class)
    {
        $this->defaultMethodBuilder = $class;

        return $this;
    }

    /**
     * Determine what class will be used by {@link createInjectionMethod()}
     *
     * Mainly to provide the ability to temporarily override the class used.
     *
     * @return string
     */
    public function getMethodBuilder()
    {
        return $this->defaultMethodBuilder;
    }

    /**
     * @return InjectionMethod[]
     */
    public function getInjectionMethods()
    {
        return $this->injectionMethods;
    }
}
