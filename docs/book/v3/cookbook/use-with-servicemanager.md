# Usage With laminas-servicemanager

laminas-di is designed to play and integrate well with laminas-servicemanager.  When
you are using [laminas-component-installer](https://docs.laminas.dev/laminas-component-installer/),
you just need to install laminas-di via composer and you're done.

## Service Factories For DI instances

laminas-di ships with two service factories to provide the
`Laminas\Di\InjectorInterface` implementation.

- `Laminas\Di\Container\ConfigFactory`: Creates a config instance by using the `"config"` service.

- `Laminas\Di\Container\InjectorFactory`: Creates the injector instance that uses a
  `Laminas\Di\ConfigInterface` service, if available.

```php
use Laminas\Di;
use Laminas\Di\Container;

$serviceManager->setFactory(Di\ConfigInterface::class, Container\ConfigFactory::class);
$serviceManager->setFactory(Di\InjectorInterface::class, Container\InjectorFactory::class);
```

## Abstract/Generic Service Factory

This component ships with an generic factory
`Laminas\Di\Container\AutowireFactory`. This factory is suitable as an abstract
service factory for use with laminas-servicemanager.

You can also use it to create instances with laminas-di using an IoC container
(e.g. inside a service factory):

```php
use Laminas\Di\Container\AutowireFactory;
(new AutowireFactory())->__invoke($container, MyClassname::class);
```

Or you can use it as factory in your service configuration directly:

```php
return [
    'factories' => [
        SomeClass::class => \Laminas\Di\Container\AutowireFactory::class,
    ],
];
```


## Service Factory For AoT Code Generation

laminas-di also provides a factory for `Laminas\Di\CodeGenerator\InjectorGenerator`.
This factory (`Laminas\Di\Container\GeneratorFactory`) is also auto registered by
the `Module` and `ConfigProvider` classes for laminas-mvc and Mezzio.
