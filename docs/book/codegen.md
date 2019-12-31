# Code Generator

laminas-di comes with [Ahead-of-Time (AoT)](https://en.wikipedia.org/wiki/Ahead-of-time_compilation)
generators to create optimized code for production. These generators will
inspect the provided classes, resolve their dependencies, and generate factories
based on these results.

> ### Requirements
>
> This feature requires [laminas-code](https://docs.laminas.dev/laminas-code/),
> which you can add to your project using Composer:
>
> ```bash
> $ composer require laminas/laminas-code
> ```

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
$resolver = new DependencyResolver(new RuntimeDefinition(), $config)
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
