<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\CodeGenerator;

use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Laminas\Di\ConfigInterface;
use Laminas\Di\Definition\DefinitionInterface;
use Laminas\Di\Resolver\DependencyResolverInterface;

/**
 * Generator for the depenendency injector
 *
 * Generates a Injector class that will use a generated factory for a requested
 * type, if available. This factory will contained pre-resolved dependencies
 * from the provided configuration, definition and resolver instances.
 */
class InjectorGenerator
{
    use GeneratorTrait;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var DependencyResolverInterface
     */
    private $resolver;

    /**
     * @deprecated
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * @var int
     */
    private $factoryIndex = 0;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var FactoryGenerator
     */
    private $factoryGenerator;

    /**
     * @var AutoloadGenerator
     */
    private $autoloadGenerator;

    /**
     * Constructs the compiler instance
     *
     * @param ConfigInterface $config The configuration to compile from
     * @param DependencyResolverInterface $resolver The resolver to utilize
     * @param string $namespace Namespace to use for generated class; defaults
     *     to Laminas\Di\Generated.
     */
    public function __construct(
        ConfigInterface $config,
        DependencyResolverInterface $resolver,
        ?string $namespace = null
    ) {
        $this->config = $config;
        $this->resolver = $resolver;
        $this->namespace = $namespace ? : 'Laminas\Di\Generated';
        $this->factoryGenerator = new FactoryGenerator($config, $resolver, $this->namespace . '\Factory');
        $this->autoloadGenerator = new AutoloadGenerator($this->namespace);
    }

    /**
     * Generate injector
     *
     * @param array $factories
     */
    private function generateInjector(array $factories)
    {
        $listFile = new FileGenerator();
        $listFile->setFilename($this->outputDirectory . '/factories.php')
            ->setDocBlock(new DocBlockGenerator('AUTO GENERATED FACTORY LIST'))
            ->setBody('return ' . var_export($factories, true) . ';');

        $class = new ClassGenerator('GeneratedInjector', $this->namespace);
        $classFile = new FileGenerator();

        $loadFactoryCode = '$this->factories = require __DIR__ . \'/factories.php\';';
        $class->setExtendedClass('\\' . AbstractInjector::class)
            ->addMethod('loadFactoryList', [], MethodGenerator::FLAG_PUBLIC, $loadFactoryCode);

        $classFile->setFilename($this->outputDirectory . '/GeneratedInjector.php')
            ->setDocBlock(new DocBlockGenerator('AUTO GENERATED DEPENDENCY INJECTOR'))
            ->setNamespace($class->getNamespaceName())
            ->setClass($class);

        $listFile->write();
        $classFile->write();
    }

    /**
     * @param string $class
     * @param array $factories
     */
    private function generateTypeFatory(string $class, array &$factories)
    {
        if (isset($factories[$class])) {
            return;
        }

        try {
            $factory = $this->factoryGenerator->generate($class);

            if ($factory) {
                $factories[$class] = $factory;
            }
        } catch (\Exception $e) {
            // TODO: logging/notifying ...
        }
    }

    /**
     * @return void
     */
    private function generateAutoload()
    {
        $addFactoryPrefix = function ($value) {
            return 'Factory/' . $value;
        };

        $classmap = array_map($addFactoryPrefix, $this->factoryGenerator->getClassmap());
        $classmap[$this->namespace . '\\GeneratedInjector'] = 'GeneratedInjector.php';

        $this->autoloadGenerator->generate($classmap);
    }

    /**
     * Generate the injector
     *
     * This will generate the injector and its factories into the output directory
     *
     * @param string[] $classes
     */
    public function generate($classes = [])
    {
        $this->ensureOutputDirectory();
        $this->factoryGenerator->setOutputDirectory($this->outputDirectory . '/Factory');
        $this->autoloadGenerator->setOutputDirectory($this->outputDirectory);
        $factories = [];

        foreach ($classes as $class) {
            $this->generateTypeFatory((string)$class, $factories);
        }

        foreach ($this->config->getConfiguredTypeNames() as $type) {
            $this->generateTypeFatory($type, $factories);
        }

        $this->generateAutoload();
        $this->generateInjector($factories);
    }
}
