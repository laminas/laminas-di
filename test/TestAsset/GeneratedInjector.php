<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset;

use Laminas\Di\InjectorInterface;

class GeneratedInjector implements InjectorInterface
{
    private InjectorInterface $injector;

    public function __construct(InjectorInterface $injector)
    {
        $this->injector = $injector;
    }

    public function getInjector(): InjectorInterface
    {
        return $this->injector;
    }

    public function canCreate(string $name): bool
    {
        return $this->injector->canCreate($name);
    }

    /** @return object */
    public function create(string $name, array $options = [])
    {
        return $this->injector->create($name, $options);
    }
}
