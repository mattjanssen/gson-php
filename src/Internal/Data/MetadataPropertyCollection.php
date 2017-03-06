<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\Data;

use Tebru\Gson\PropertyMetadata;

/**
 * Class MetadataPropertyCollection
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class MetadataPropertyCollection
{
    /**
     * An array of property metadata objects
     *
     * @var PropertyMetadata[]
     */
    private $properties = [];

    /**
     * Add a property metadata object
     *
     * @param PropertyMetadata $property
     */
    public function add(PropertyMetadata $property)
    {
        $this->properties[$property->getName()] = $property;
    }

    /**
     * Get property metadata by its name, returns null if the property
     * doesn't exist.
     *
     * @param string $name
     * @return PropertyMetadata
     */
    public function get($name)
    {
        return $this->properties[$name];
    }
}
