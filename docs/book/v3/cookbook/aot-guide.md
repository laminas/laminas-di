# Using AoT with Mezzio and laminas-servicemanager

This guide will show you how you can use laminas-di's Ahead-of-Time (AoT) compiler
to make your [Mezzio](https://docs.mezzio.dev/mezzio)
application production ready when it uses laminas-di.

You will learn how to:

- Add a script to run the compilation.
- Use the generated injector with laminas-servicemanager.
- Use the generated factories with laminas-servicemanager.

## 1. Create project and add laminas-di

For this guide, we will use an [mezzio application](https://docs.mezzio.dev/mezzio/)
built from the skeleton with laminas-servicemanager as its IoC container.

If you have already set up a project with laminas-di, you can skip this step.

First, we'll create a new project:

```bash
$ composer create-project mezzio/mezzio-skeleton laminas-di-aot-example
```

Pick the components you want to use. We will be using laminas-servicemanager
and a "Modular" layout for this example.

Once you are done, enter the newly created project's working directory:

```bash
$ cd laminas-di-aot-example
```

Now add laminas-di with composer:

```bash
$ composer require laminas/laminas-di
```

> ### Possible version conflicts
>
> Please make sure that laminas-di version 3.x is installed. When you are
> upgrading from laminas-di version 2.x, you may have to remove
> `laminas-servicemanager-di` because version 3.x makes this package obsolete and
> therefore conflicts with it.
>
> You can ensure version 3.x is installed by adding a version constraint to
> composer's require command:
>
> ```bash
> $ composer require laminas/laminas-di:^3.0
> ```
>
> This approach will also notify you if there are conflicts with installing v3.

> ### Additional requirements for version 3.0.x
>
> Before version 3.1, `laminas/laminas-code` was required to be
> added individually to your project for generating AoT code. Since version
> 3.1 this is no longer necessary.

The component installer should ask you where to inject the config provider. Pick
option 1, which usually is `config/config.php`. If not, or you cannot use the
component installer, you will need to add it manually by adding an entry for
`\Laminas\Di\ConfigProvider::class` within your application configuration
example:

```php
// config/config.php:

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

// ...

$aggregator = new ConfigAggregator([
    // Add Laminas\Di
    \Laminas\Di\ConfigProvider::class,

    // ...
], $cacheConfig['config_cache_path']);

// ...
```

## 2. Make your project ready for AoT

To follow the modular principle of our mezzio app, we will put the AoT
related configurations and generated code in a separate module called `AppAoT`.

By default, skeleton applications include the mezzio-tooling component,
which allows you to do this in a single step:

```bash
$ ./vendor/bin/mezzio module:create AppAoT
```

If the tooling is present and the above command is successful, you can now skip
to the next step. Otherwise, continue on to manually create your module.

First, create the initial directory structure:

```bash
$ mkdir src/AppAoT/src
```

Next, create a config provider class in `src/AppAoT/src/ConfigProvider.php`:

```php
namespace AppAoT;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies()
    {
        return [
            'auto' => [
                'aot' => [
                    'namespace' => __NAMESPACE__ . '\\Generated',
                    'directory' => __DIR__ . '/../gen',
                ],
            ],
        ];
    }
}
```

Add this new class to the the beginning of your `config/config.php` file's
`ConfigAggregator` settings:

```php
$aggregator = new ConfigAggregator([
    \AppAoT\ConfigProvider::class
    // Add Laminas\Di
    \Laminas\Di\ConfigProvider::class,

    // ...
]);
```

In order for your application to find this class, we need to add an autoloading
rule. Find the `psr-4` autoloader section of your `composer.json`, and add an
entry for your new `AppAot` namespace as follows:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/App/src/",
            "AppAoT\\": "src/AppAoT/src/",
            "AppAoT\\Generated\\": "src/AppAoT/gen/"
        }
    },
    ...
}
```

> Note that we defined `AppAoT\\Generated\\` which will point to the code
> we generate from laminas-di in the next steps.

Finally, update your autoloader:

```bash
$ composer dump-autoload
```

## 3. Add some auto-wiring

Because laminas-di can provide autowiring for us, we can remove configuration that
already exists within our `App` module. Edit the file
`src/App/src/ConfigProvider.php` and comment out the entries shown below:

```php
    public function getDependencies()
    {
        return [
            'invokables' => [
                // Action\PingAction::class => Action\PingAction::class,
            ],
            'factories'  => [
                // Action\HomePageAction::class => Action\HomePageFactory::class,
            ],
        ];
    }
```

We can also now remove the `HomePageFactory` referenced in that method:

```bash
$ rm src/App/src/HomePageFactory.php
```

The default actions (`HomePageAction` and `PingAction`) now use auto wiring!

## 4. Add a code generator command script

We will now add a simple script in the `bin/` directory, which we will also add
to our `composer.json`scripts section, to generate factories.

In the real world, you might use a console implementation such as
symfony/console for scripts such as these.

Add the generator script `bin/di-generate-aot.php`:

```php
namespace AppAoT;

use Psr\Container\ContainerInterface;
use Laminas\Code\Scanner\DirectoryScanner;
use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Di\Config;

require __DIR__ . '/../vendor/autoload.php';

// Define the source directories to scan for classes for which
// to generate AoT factories:
$directories = [
    __DIR__ . '/../src/App/src',
];

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';
$scanner = new DirectoryScanner($directories);
$generator = $container->get(InjectorGenerator::class);

$generator->generate($scanner->getClassNames());
```

> ### Manually creating a generator instance
>
> Before version 3.1, no service factory existed for the generator. Below is an
> example demonstrating manual creation of the generator:
>
> ```php
> namespace AppAoT;
>
> use Psr\Container\ContainerInterface;
> use Laminas\Code\Scanner\DirectoryScanner;
> use Laminas\Di\CodeGenerator\InjectorGenerator;
> use Laminas\Di\Config;
> use Laminas\Di\ConfigInterface;
> use Laminas\Di\Definition\RuntimeDefinition;
> use Laminas\Di\Resolver\DependencyResolver;
>
> require __DIR__ . '/../vendor/autoload.php';
>
> $directories = [
>     __DIR__ . '/../src/App/src',
> ];
>
> // Generator dependencies. You might put this in a service factory
> // in a real-life scenario.
>
> /** @var ContainerInterface $container */
> $container = require __DIR__ . '/../config/container.php';
> $config = $container->get(ConfigInterface::class);
> $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
>
> // This is important; we want to use configured aliases of the service manager.
> $resolver->setContainer($container);
>
> $scanner = new DirectoryScanner($directories);
> $generator = new InjectorGenerator($config, $resolver, __NAMESPACE__ . '\Generated');
> $generator->setOutputDirectory(__DIR__ . '/../src/AppAoT/gen');
> $generator->generate($scanner->getClassNames());
> ```

To add the Composer script, edit `composer.json` and add the following to the
`scripts` section:

```json
{
    "scripts": {
        "di-generate-aot": [
            "rm -vfr src/AppAoT/gen",
            "php bin/di-generate-aot.php"
        ]
    }
}
```

When running the compiler with `composer di-generate-aot`, it will generate
the following files:

![screenshot-gen-result](img/aot-gen-result.png)

## 5. Add AoT to the service manager

Now we need to make the service manager use the AoT code.

First, we'll use a delegator `Laminas\Di\GeneratedInjectorDelegator` to decorate
the DI injector with the AoT version. Decorating the injector ensures that your
factories that utilize `Laminas\Di\Container\AutowireFactory` will benefit from AoT
as well. We need to add configuration to the `ConfigProvider` class we created
in step 2:

> **Important:** After this step, the application will **always** use the
> generated factories, if present. If you change any dependencies, you will need
> to run `composer di-aot-generation` again, or remove the generated code in
> `src/AppAoT/gen/` and use runtime wiring.

```php
namespace AppAoT;

use Laminas\Di\GeneratedInjectorDelegator;
use Laminas\Di\InjectorInterface;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies()
    {
        return [
            'auto' => [
                'aot' => [
                    'namespace' => __NAMESPACE__ . '\\Generated',
                    'directory' => __DIR__ . '/../gen',
                ],
            ],
            'factories' => $this->getGeneratedFactories(),
            'delegators' => [
                InjectorInterface::class => [
                    GeneratedInjectorDelegator::class,
                ],
            ],
        ];
    }

    private function getGeneratedFactories()
    {
        // The generated factories.php file is compatible with
        // laminas-servicemanager's factory configuration.
        // This avoids using the abstract AutowireFactory, which
        // improves performance a bit since we spare some lookups.

        if (file_exists(__DIR__ . '/../gen/factories.php')) {
            return include __DIR__ . '/../gen/factories.php';
        }

        return [];
    }
}
```

> ### Custom delegator factory (before version 3.2)
> 
> The `Laminas\Di\GeneratedInjectorDelegator` class is available since version 3.2. For
> prior versions of laminas-di, a custom delegator factory must be provided.
>
> ```php
> namespace AppAoT;
> 
> use AppAoT\Generated\GeneratedInjector;
> use Interop\Container\ContainerInterface;
> use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
> 
> class GeneratedInjectorDelegator implements DelegatorFactoryInterface
> {
>     public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
>     {
>         $injector = $callback();
> 
>         if (class_exists(GeneratedInjector::class)) {
>             return new GeneratedInjector($injector);
>         }
> 
>         return $injector;
>     }
> }
> ```
