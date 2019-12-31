<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Definition\Annotation;

use Laminas\Code\Annotation\AnnotationInterface;

/**
 * Annotation for injection endpoints for dependencies
 */
class Inject implements AnnotationInterface
{
    /**
     * @var mixed
     */
    protected $content = null;

    /**
     * {@inheritDoc}
     */
    public function initialize($content)
    {
        $this->content = $content;
    }
}
