# Quick Start

The DI component provides an auto wiring strategy which implements
[constructor injection](https://en.wikipedia.org/wiki/Dependency_injection#Constructor_injection).

It utilizes [PSR-11](psr-11.md) containers to obtain required services, so it
can be paired with any IoC container that implements this interface, such as
[laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/).

## 1. Installation

If you haven't already, [install Composer](https://getcomposer.org/).
Once you have, you can install laminas-di:

```bash
$ composer require laminas/laminas-di
```

## 2. Configuring the injector

You can now create and configure an injector instance. The injector accepts an
instance of `Laminas\Di\ConfigInterface`. This can be provided by passing
`Laminas\Di\Config`, which accepts a PHP array to its constructor:

```php
use Laminas\Di\Injector;
use Laminas\Di\Config;

$injector = new Injector(new Config([
    'preferences' => [
        MyInterface::class => MyImplementation::class,
    ],
]));
```

This config implementation accepts a veriety of options. Refer to the
[Configuration](config.md) chapter for full details.

## 3. Creating instances

Finally, you can create new instances of a specific class or alias by using the
`create()` method:

```php
$instance = $injector->create(MyClass::class);
```

The only precondition is that the class you provide to `create()` must exist (or
be autoloadable).  If this is not the case, the injector will fail with an
exception.

The `create()` method will _always_ create a new instance of the given class. If
you need a shared instance, you can associate an IoC container implementing
PSR-11 with the injector:

```php
$injector = new Injector($config, $container);

$sharedInstance = $injector->getContainer()->get(MyClass::class);
```

By default, the injector creates and uses an instance of
`Laminas\Di\DefaultContainer` if no container is provided to it.  This
implementation is quite limited, however, and we recommend you use a more
featureful container with the injector, such as
[laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/).
Refer to the [Usage with PSR-11 containers](cookbook/use-with-psr-containers.md)
and [Usage with laminas-servicemanager](cookbook/use-with-servicemanager.md)
chapters for details.
