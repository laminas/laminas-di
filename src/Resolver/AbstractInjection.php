<?php
// phpcs:ignoreFile
/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Di\Resolver;

use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

trigger_error(
    sprintf(
        '%s is deprecated, please migrate to %s',
        AbstractInjection::class,
        InjectionInterface::class
    ),
    E_USER_DEPRECATED
);

/**
 * @deprecated Since 3.1.0
 *
 * @see InjectionInterface
 *
 * @codeCoverageIgnore Deprecated
 */
abstract class AbstractInjection
{
    /** @var string */
    private $parameterName;

    public function setParameterName(string $name): self
    {
        $this->parameterName = $name;
        return $this;
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    abstract public function export(): string;

    abstract public function isExportable(): bool;
}
