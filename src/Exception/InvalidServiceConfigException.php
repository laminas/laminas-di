<?php

namespace Laminas\Di\Exception;

use Psr\Container\ContainerExceptionInterface;

class InvalidServiceConfigException extends LogicException implements ContainerExceptionInterface
{
}
