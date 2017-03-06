<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal;

use Tebru\Gson\ClassMetadata;
use Tebru\Gson\Internal\Data\AnnotationSet;

/**
 * Class DefaultClassMetadata
 *
 * Represents a class an its annotations
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class DefaultClassMetadata implements ClassMetadata
{
    /**
     * The class name
     *
     * @var string
     */
    private $name;

    /**
     * The class annotations
     *
     * @var AnnotationSet
     */
    private $annotations;

    /**
     * Constructor
     *
     * @param string $name
     * @param AnnotationSet $annotations
     */
    public function __construct($name, AnnotationSet $annotations)
    {
        $this->name = $name;
        $this->annotations = $annotations;
    }

    /**
     * Get the class name as a string
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get all class annotations
     *
     * @return AnnotationSet
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Get a specific annotation by class name, returns null if the annotation
     * doesn't exist.
     *
     * @param string $annotationClass
     * @return null|object
     */
    public function getAnnotation($annotationClass)
    {
        return $this->annotations->getAnnotation($annotationClass, AnnotationSet::TYPE_CLASS);
    }
}
