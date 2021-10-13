# Code Generator

laminas-di comes with [Ahead-of-Time (AoT)](https://en.wikipedia.org/wiki/Ahead-of-time_compilation)
generators to create optimized code for production. These generators will
inspect the provided classes, resolve their dependencies, and generate factories
based on these results.

<!-- markdownlint-disable-next-line header-increment -->
> ### Removal of laminas-code dependencies
>
> Before version 3.1, this feature required [laminas-code](https://docs.laminas.dev/laminas-code/),
> which you can add to your project using Composer:
>
> ```bash
> $ composer require --dev laminas/laminas-code
> ```
>
> **Since version 3.1 and up, this is no longer required.**

## Generating an optimized injector

The `Laminas\Di\CodeGenerator\InjectorGenerator` class offers an implementation to
generate an optimized injector based on the runtime configuration and a resolver
instance.

```php
use Laminas\Di\Config;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Resolver\DependencyResolver;
use Laminas\Di\CodeGenerator\InjectorGenerator;

$config = new Config();
$resolver = new DependencyResolver(new RuntimeDefinition(), $config);
$generator = new InjectorGenerator($config, $resolver);

// It is highly recommended to set the container that is used at runtime:
$resolver->setContainer($container);
$generator->setOutputDirectory('/path/to/generated/files');
$generator->generate([
    MyClassA::class,
    MyClassB::class,
    // ...
]);
```

You can also utilize `Laminas\Code\Scanner` to scan your code for classes:

```php
$scanner = new DirectoryScanner(__DIR__);
$generator->generate($scanner->getClassNames());
```

## MVC and Mezzio integration

When you are using laminas-di's `ConfigProvider` with Mezzio or consuming the
`Module` class via laminas-mvc, you can obtain the generator instance from the
service manager:

```php
$generator = $serviceManager->get(\Laminas\Di\CodeGenerator\InjectorGenerator::class);
```

### AoT Config Options

The service factory uses options in your `config` service, located under the key
`dependencies.auto.aot`. This should be defined as an associative array of
options for creating the code generator instance. This array respects the
following keys (unknown keys are ignored):

- `namespace`: This will be used as base namespace to prefix the namespace of
  the generated classes.  It will be passed to the constructor of
  `Laminas\Di\CodeGenerator\InjectorGenerator`; the default value is
  `Laminas\Di\Generated`.

- `directory`: The directory where the generated PHP files will be stored. If
  this value is not provided, you will need to set it with the generator's
  `setOutputDirectory()` method before calling `generate()`.

- `logger`: must be resolvable in container and must be an instance of `Psr\Log\LoggerInterface.`
  By default `Psr\Log\NullLogger` is used. See the [Logging section](#logging) for details.

Below is an example detailing configuration of the generator factory:

```php
return [
    'dependencies' => [
        'auto' => [
            'aot' => [
                'namespace' => 'AppAoT\Generated',
                'directory' => __DIR__ . '/../gen',
                'logger' => Psr\Log\LoggerInterface::class,
            ],
        ],
    ],
];
```

## Logging

The `InjectorGenerator` allows passing a [PSR-3 logger](http://www.php-fig.org/psr/psr-3/) instance
via an optional fourth constructor parameter.

The generator will log the following information:

- When a factory is about to be generated for a class or alias (Log level: Debug)
- When the factory generation caused an exception (Log level: Error)
