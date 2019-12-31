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
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;

class AutoloadGenerator
{
    use GeneratorTrait;

    private $namespace;

    /**
     * @param string $namespace
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    private function generateAutoloaderClass(array &$classmap)
    {
        $class = new ClassGenerator('Autoloader');
        $classmapValue = new PropertyValueGenerator(
            $classmap,
            PropertyValueGenerator::TYPE_ARRAY_SHORT,
            PropertyValueGenerator::OUTPUT_MULTIPLE_LINE
        );

        $registerCode = 'if (!$this->registered) {'.PHP_EOL
            . '    spl_autoload_register($this);'.PHP_EOL
            . '    $this->registered = true;'.PHP_EOL
            . '}'.PHP_EOL
            . 'return $this;';

        $unregisterCode = 'if ($this->registered) {'.PHP_EOL
            . '    spl_autoload_unregister($this);'.PHP_EOL
            . '    $this->registered = false;'.PHP_EOL
            . '}'.PHP_EOL
            . 'return $this;';

        $loadCode = 'if (isset($this->classmap[$class])) {'.PHP_EOL
            . '    include __DIR__ . \'/\' . $this->classmap[$class];'.PHP_EOL
            . '}';

        $class
            ->addProperty('registered', false, PropertyGenerator::FLAG_PRIVATE)
            ->addProperty('classmap', $classmapValue, PropertyGenerator::FLAG_PRIVATE)
            ->addMethod('register', [], MethodGenerator::FLAG_PUBLIC, $registerCode)
            ->addMethod('unregister', [], MethodGenerator::FLAG_PUBLIC, $unregisterCode)
            ->addMethod('load', ['class'], MethodGenerator::FLAG_PUBLIC, $loadCode)
            ->addMethod('__invoke', ['class'], MethodGenerator::FLAG_PUBLIC, '$this->load($class);');

        $file = new FileGenerator();
        $file
            ->setDocBlock(new DocBlockGenerator('Generated autoloader for Laminas\Di'))
            ->setNamespace($this->namespace)
            ->setClass($class)
            ->setFilename($this->outputDirectory . '/Autoloader.php');

        $file->write();
    }

    /**
     * @param array $classmap
     */
    public function generate(array &$classmap)
    {
        $this->ensureOutputDirectory();
        $this->generateAutoloaderClass($classmap);

        $code = "require_once __DIR__ . '/Autoloader.php';\n"
            . 'return (new Autoloader())->register();';

        $file = new FileGenerator();
        $file
            ->setDocBlock(new DocBlockGenerator('Generated autoload file for Laminas\Di'))
            ->setNamespace($this->namespace)
            ->setBody($code)
            ->setFilename($this->outputDirectory.'/autoload.php')
            ->write();
    }
}
