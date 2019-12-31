<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\ServiceLocator;

use Laminas\Di\Config;
use Laminas\Di\Definition\Builder;
use Laminas\Di\Definition\BuilderDefinition as Definition;
use Laminas\Di\Di;
use Laminas\Di\ServiceLocator\Generator as ContainerGenerator;
use PHPUnit_Framework_TestCase as TestCase;

class GeneratorTest extends TestCase
{
    protected $tmpFile = false;

    /**
     * @var \Laminas\Di\Di
     */
    protected $di = null;

    public function setUp()
    {
        $this->tmpFile = false;
        $this->di = new Di;
    }

    public function tearDown()
    {
        if ($this->tmpFile) {
            unlink($this->tmpFile);
            $this->tmpFile = false;
        }
    }

    public function getTmpFile()
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'zdi');
        return $this->tmpFile;
    }

    public function createDefinitions()
    {
        $inspect = new Builder\PhpClass();
        $inspect->setName('LaminasTest\Di\TestAsset\InspectedClass');
        $inspectCtor = new Builder\InjectionMethod();
        $inspectCtor->setName('__construct')
                    ->addParameter('foo', 'composed')
                    ->addParameter('baz', null);
        $inspect->addInjectionMethod($inspectCtor);

        $composed = new Builder\PhpClass();
        $composed->setName('LaminasTest\Di\TestAsset\ComposedClass');

        $struct = new Builder\PhpClass();
        $struct->setName('LaminasTest\Di\TestAsset\Struct');
        $structCtor = new Builder\InjectionMethod();
        $structCtor->setName('__construct')
                   ->addParameter('param1', null)
                   ->addParameter('param2', 'inspect');

        $definition = new Definition();
        $definition->addClass($inspect)
                   ->addClass($composed)
                   ->addClass($struct);
        $this->di->definitions()->unshift($definition);

        $data = array(
            'instance' => array(
                'alias' => array(
                    'composed' => 'LaminasTest\Di\TestAsset\ComposedClass',
                    'inspect'  => 'LaminasTest\Di\TestAsset\InspectedClass',
                    'struct'   => 'LaminasTest\Di\TestAsset\Struct',
                ),
                'preferences' => array(
                    'composed' => array('composed'),
                    'inspect'  => array('inspect'),
                    'struct'   => array('struct'),
                ),
                'LaminasTest\Di\TestAsset\InspectedClass' => array( 'parameters' => array(
                    'baz' => 'BAZ',
                )),
                'LaminasTest\Di\TestAsset\Struct' => array( 'parameters' => array(
                    'param1' => 'foo',
                )),
            ),
        );
        $configuration = new Config($data);
        $configuration->configure($this->di);
    }

    public function buildContainerClass($name = 'Application')
    {
        $this->createDefinitions();
        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass($name);
        $builder->getCodeGenerator($this->getTmpFile())->write();
        $this->assertFileExists($this->tmpFile);
    }

    /**
     * @group one
     */
    public function testCreatesContainerClassFromConfiguredDependencyInjector()
    {
        $this->buildContainerClass();

        $tokens = token_get_all(file_get_contents($this->tmpFile));
        $count  = count($tokens);
        $found  = false;
        $value  = false;
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token)) {
                continue;
            }
            if (T_CLASS == $token[0]) {
                $found = true;
                $value = false;
                do {
                    $i++;
                    $token = $tokens[$i];
                    if (is_string($token)) {
                        $id = null;
                    } else {
                        list($id, $value) = $token;
                    }
                } while (($i < $count) && ($id != T_STRING));
                break;
            }
        }
        $this->assertTrue($found, "Class token not found");
        $this->assertContains('Application', $value);
    }

    public function testCreatesContainerClassWithCasesForEachService()
    {
        $this->buildContainerClass();

        $tokens   = token_get_all(file_get_contents($this->tmpFile));
        $count    = count($tokens);
        $services = array();
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token)) {
                continue;
            }
            if ('T_CASE' == token_name($token[0])) {
                do {
                    $i++;
                    if ($i >= $count) {
                        break;
                    }
                    $token = $tokens[$i];
                    if (is_string($token)) {
                        $id = $token;
                    } else {
                        $id = $token[0];
                    }
                } while (($i < $count) && ($id != T_CONSTANT_ENCAPSED_STRING));
                if (is_array($token)) {
                    $services[] = preg_replace('/\\\'/', '', $token[1]);
                }
            }
        }
        $expected = array(
            'composed',
            'LaminasTest\Di\TestAsset\ComposedClass',
            'inspect',
            'LaminasTest\Di\TestAsset\InspectedClass',
            'struct',
            'LaminasTest\Di\TestAsset\Struct',
        );
        $this->assertEquals(count($expected), count($services), var_export($services, 1));
        foreach ($expected as $service) {
            $this->assertContains($service, $services);
        }
    }

    public function testCreatesContainerClassWithMethodsForEachServiceAndAlias()
    {
        $this->buildContainerClass();
        $tokens  = token_get_all(file_get_contents($this->tmpFile));
        $count   = count($tokens);
        $methods = array();
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token)) {
                continue;
            }
            if ("T_FUNCTION" == token_name($token[0])) {
                $value = false;
                do {
                    $i++;
                    $token = $tokens[$i];
                    if (is_string($token)) {
                        $id = null;
                    } else {
                        list($id, $value) = $token;
                    }
                } while (($i < $count) && (token_name($id) != 'T_STRING'));
                if ($value) {
                    $methods[] = $value;
                }
            }
        }
        $expected = array(
            'get',
            'getLaminasTestDiTestAssetComposedClass',
            'getComposed',
            'getLaminasTestDiTestAssetInspectedClass',
            'getInspect',
            'getLaminasTestDiTestAssetStruct',
            'getStruct',
        );
        $this->assertEquals(count($expected), count($methods), var_export($methods, 1));
        foreach ($expected as $method) {
            $this->assertContains($method, $methods);
        }
    }

    public function testAllowsRetrievingClassFileCodeGenerationObject()
    {
        $this->createDefinitions();
        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Application');
        $codegen = $builder->getCodeGenerator();
        $this->assertInstanceOf('Laminas\Code\Generator\FileGenerator', $codegen);
    }

    public function testCanSpecifyNamespaceForGeneratedPhpClassfile()
    {
        $this->createDefinitions();
        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Context')
                ->setNamespace('Application');
        $codegen = $builder->getCodeGenerator();
        $this->assertEquals('Application', $codegen->getNamespace());
    }

    /**
     * @group nullargs
     */
    public function testNullAsOnlyArgumentResultsInEmptyParameterList()
    {
        $this->markTestIncomplete('Null arguments are currently unsupported');
        $opt = new Builder\PhpClass();
        $opt->setName('LaminasTest\Di\TestAsset\OptionalArg');
        $optCtor = new Builder\InjectionMethod();
        $optCtor->setName('__construct')
                ->addParameter('param', null);
        $opt->addInjectionMethod($optCtor);
        $def = new Definition();
        $def->addClass($opt);
        $this->di->setDefinition($def);

        $cfg = new Config(array(
            'instance' => array(
                'alias' => array('optional' => 'LaminasTest\Di\TestAsset\OptionalArg'),
            ),
            'properties' => array(
                'LaminasTest\Di\TestAsset\OptionalArg' => array('param' => null),
            ),
        ));
        $cfg->configure($this->di);

        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Container');
        $codeGen = $builder->getCodeGenerator();
        $classBody = $codeGen->generate();
        $this->assertNotContains('NULL)', $classBody, $classBody);
    }

    /**
     * @group nullargs
     */
    public function testNullAsLastArgumentsResultsInTruncatedParameterList()
    {
        $this->markTestIncomplete('Null arguments are currently unsupported');
        $struct = new Builder\PhpClass();
        $struct->setName('LaminasTest\Di\TestAsset\Struct');
        $structCtor = new Builder\InjectionMethod();
        $structCtor->setName('__construct')
                   ->addParameter('param1', null)
                   ->addParameter('param2', null);
        $struct->addInjectionMethod($structCtor);

        $dummy = new Builder\PhpClass();
        $dummy->setName('LaminasTest\Di\TestAsset\DummyParams')
              ->setInstantiator(array('LaminasTest\Di\TestAsset\StaticFactory', 'factory'));

        $staticFactory = new Builder\PhpClass();
        $staticFactory->setName('LaminasTest\Di\TestAsset\StaticFactory');
        $factory = new Builder\InjectionMethod();
        $factory->setName('factory')
                ->addParameter('struct', 'struct')
                ->addParameter('params', null);
        $staticFactory->addInjectionMethod($factory);

        $def = new Definition();
        $def->addClass($struct)
            ->addClass($dummy)
            ->addClass($staticFactory);

        $this->di->setDefinition($def);

        $cfg = new Config(array(
            'instance' => array(
                'alias' => array(
                    'struct'  => 'LaminasTest\Di\TestAsset\Struct',
                    'dummy'   => 'LaminasTest\Di\TestAsset\DummyParams',
                    'factory' => 'LaminasTest\Di\TestAsset\StaticFactory',
                ),
                'properties' => array(
                    'LaminasTest\Di\TestAsset\Struct' => array(
                        'param1' => 'foo',
                        'param2' => 'bar',
                    ),
                    'LaminasTest\Di\TestAsset\StaticFactory' => array(
                        'params' => null,
                    ),
                ),
            ),
        ));
        $cfg->configure($this->di);

        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Container');
        $codeGen = $builder->getCodeGenerator();
        $classBody = $codeGen->generate();
        $this->assertNotContains('NULL)', $classBody, $classBody);
    }

    /**
     * @group nullargs
     */
    public function testNullArgumentsResultInEmptyMethodParameterList()
    {
        $this->markTestIncomplete('Null arguments are currently unsupported');
        $opt = new Builder\PhpClass();
        $opt->setName('LaminasTest\Di\TestAsset\OptionalArg');
        $optCtor = new Builder\InjectionMethod();
        $optCtor->setName('__construct')
                ->addParameter('param', null);
        $optInject = new Builder\InjectionMethod();
        $optInject->setName('inject')
                  ->addParameter('param1', null)
                  ->addParameter('param2', null);
        $opt->addInjectionMethod($optCtor)
            ->addInjectionMethod($optInject);

        $def = new Definition();
        $def->addClass($opt);
        $this->di->setDefinition($def);

        $cfg = new Config(array(
            'instance' => array(
                'alias' => array('optional' => 'LaminasTest\Di\TestAsset\OptionalArg'),
            ),
            'properties' => array(
                'LaminasTest\Di\TestAsset\OptionalArg' => array(
                    'param'  => null,
                    'param1' => null,
                    'param2' => null,
                ),
            ),
        ));
        $cfg->configure($this->di);

        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Container');
        $codeGen = $builder->getCodeGenerator();
        $classBody = $codeGen->generate();
        $this->assertNotContains('NULL)', $classBody, $classBody);
    }

    public function testClassNamesInstantiatedDirectlyShouldBeFullyQualified()
    {
        $this->createDefinitions();
        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Context')
                ->setNamespace('Application');
        $content = $builder->getCodeGenerator()->generate();
        $count   = substr_count($content, '\LaminasTest\Di\TestAsset\\');
        $this->assertEquals(3, $count, $content);
        $this->assertNotContains('\\\\', $content);
    }
}
