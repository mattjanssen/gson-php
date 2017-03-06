<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */
namespace Tebru\Gson;


/**
 * Class PhpType
 *
 * Represents a core php type and includes methods to get information about
 * the type
 *
 * @author Nate Brunette <n@tebru.net>
 */
interface PhpType
{
    /**
     * Returns an array of generic types
     *
     * @return array
     */
    public function getGenerics();

    /**
     * Returns the class if an object, or the type as a string
     *
     * @return string
     */
    public function getType();

    /**
     * Returns true if the type matches the class, parent, full type, or one of the interfaces
     *
     * @param string $type
     * @return bool
     */
    public function isA($type);

    /**
     * Returns true if this is a string
     *
     * @return bool
     */
    public function isString();

    /**
     * Returns true if this is an integer
     *
     * @return bool
     */
    public function isInteger();

    /**
     * Returns true if this is a float
     *
     * @return bool
     */
    public function isFloat();

    /**
     * Returns true if this is a boolean
     *
     * @return bool
     */
    public function isBoolean();

    /**
     * Returns true if this is an array
     *
     * @return bool
     */
    public function isArray();

    /**
     * Returns true if this is an object
     *
     * @return bool
     */
    public function isObject();

    /**
     * Returns true if this is null
     *
     * @return bool
     */
    public function isNull();

    /**
     * Returns true if this is a resource
     *
     * @return bool
     */
    public function isResource();

    /**
     * Returns true if the type could be anything
     *
     * @return bool
     */
    public function isWildcard();

    /**
     * Returns an array of extra options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Returns a unique identifying key for this type based on
     * the full type and options
     *
     * @return string
     */
    public function getUniqueKey();

    /**
     * Return the initial type including generics
     *
     * @return string
     */
    public function __toString();
}
