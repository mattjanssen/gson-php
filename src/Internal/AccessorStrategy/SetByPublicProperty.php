<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\AccessorStrategy;

use Tebru\Gson\Internal\SetterStrategy;

/**
 * Class SetByPublicProperty
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class SetByPublicProperty implements SetterStrategy
{
    /**
     * @var string
     */
    private $propertyName;

    /**
     * Constructor
     *
     * @param string $propertyName
     */
    public function __construct($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * Set value to object by method name
     *
     * @param object $object
     * @param mixed $value
     * @return void
     */
    public function set($object, $value)
    {
        $object->{$this->propertyName} = $value;
    }
}
