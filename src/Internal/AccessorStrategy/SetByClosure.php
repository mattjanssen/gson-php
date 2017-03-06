<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\AccessorStrategy;

use Closure;
use Tebru\Gson\Internal\SetterStrategy;

/**
 * Class SetByClosure
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class SetByClosure implements SetterStrategy
{
    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $className;

    /**
     * @var Closure
     */
    private $setter;

    /**
     * Constructor
     *
     * @param string $propertyName
     * @param string $className
     */
    public function __construct($propertyName, $className)
    {
        $this->propertyName = $propertyName;
        $this->className = $className;
    }

    /**
     * Set object value by binding a closure to the class
     *
     * @param object $object
     * @param mixed $value
     * @return void
     */
    public function set($object, $value)
    {
        if (null === $this->setter) {
            $this->setter = Closure::bind(function ($object, $value, $propertyName) {
                $object->{$propertyName} = $value;
            }, null, $this->className);
        }

        $setter = $this->setter;
        $setter($object, $value, $this->propertyName);
    }
}
