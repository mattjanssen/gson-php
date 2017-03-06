<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\Data;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class PropertyCollection
 *
 * A collection of [@see Property] objects
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class PropertyCollection implements IteratorAggregate
{
    /**
     * Array of [@see Property] objects
     *
     * @var Property[]
     */
    private $elements = [];

    /**
     * Constructor
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        foreach ($properties as $property) {
            $this->add($property);
        }
    }

    /**
     * @param Property $property
     */
    public function add(Property $property)
    {
        $this->elements[$property->getSerializedName()] = $property;
    }

    /**
     * Get [@see Property] by serialized name
     *
     * @param string $name
     * @return Property|null
     */
    public function getBySerializedName($name)
    {
        if (!isset($this->elements[$name])) {
            return null;
        }

        return $this->elements[$name];
    }

    /**
     * Array of Property objects
     *
     * @return Property[]
     */
    public function toArray()
    {
        return array_values($this->elements);
    }

    /**
     * Retrieve an external iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }
}
