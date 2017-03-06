<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\AccessorStrategy;

use Tebru\Gson\Internal\SetterStrategy;

/**
 * Class SetByMethod
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class SetByMethod implements SetterStrategy
{
    /**
     * @var string
     */
    private $methodName;

    /**
     * Constructor
     *
     * @param string $methodName
     */
    public function __construct($methodName)
    {
        $this->methodName = $methodName;
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
        $object->{$this->methodName}($value);
    }
}
