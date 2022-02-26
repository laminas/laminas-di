<?php

declare(strict_types=1);

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\GeneratorTrait;
use Laminas\Di\Exception\GenerateCodeException;
use PHPUnit\Framework\TestCase;

final class GeneratorTraitTest extends TestCase
{
    public function testEnsureDirectoryGivenInvalidDirectoryNameExpectedErrorMessageContainInvalidDirectoryName(): void
    {
        $invalidDir = 'http://www.invalid-directory';

        $this->expectException(GenerateCodeException::class);
        $this->expectErrorMessage('Could not create output directory: ' . $invalidDir);

        new class (__DIR__, $invalidDir)
        {
            use GeneratorTrait;

            public function __construct(string $dir, string $otherDir)
            {
                $this->outputDirectory = $dir;

                $this->ensureDirectory($otherDir);
            }
        };
    }
}
