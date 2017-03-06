<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson;

use Tebru\Gson\Element\JsonElement;
use Tebru\Gson\Internal\Data\Property;
use Tebru\Gson\Internal\Data\PropertyCollectionFactory;
use Tebru\Gson\Internal\DefaultPhpType;
use Tebru\Gson\Internal\TypeAdapterProvider;

/**
 * Class Gson
 *
 * @author Nate Brunette <n@tebru.net>
 */
class Gson
{
    /**
     * A service to fetch the correct [@see TypeAdapter] for a given type
     *
     * @var TypeAdapterProvider
     */
    private $typeAdapterProvider;

    /**
     * A factory that reflects over class properties and returns a collection
     * of [@see Property] objects
     *
     * @var \Tebru\Gson\Internal\Data\PropertyCollectionFactory
     */
    private $propertyCollectionFactory;

    /**
     * True if we should serialize nulls
     *
     * @var bool
     */
    private $serializeNull;

    /**
     * Constructor
     *
     * @param TypeAdapterProvider $typeAdapterProvider
     * @param PropertyCollectionFactory $propertyCollectionFactory
     * @param bool $serializeNull
     */
    public function __construct(
        TypeAdapterProvider $typeAdapterProvider,
        PropertyCollectionFactory $propertyCollectionFactory,
        $serializeNull
    ) {
        $this->typeAdapterProvider = $typeAdapterProvider;
        $this->propertyCollectionFactory = $propertyCollectionFactory;
        $this->serializeNull = $serializeNull;
    }

    /**
     * Create a new builder object
     *
     * @return GsonBuilder
     */
    public static function builder()
    {
        return new GsonBuilder();
    }

    /**
     * Converts an object to a json string
     *
     * @param mixed $object
     * @return string
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     */
    public function toJson($object)
    {
        $type = DefaultPhpType::createFromVariable($object);
        $typeAdapter = $this->typeAdapterProvider->getAdapter($type);

        return $typeAdapter->writeToJson($object, $this->serializeNull);
    }

    /**
     * Converts a json string to a valid json type
     *
     * @param string $json
     * @param object|string $type
     * @return mixed
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     */
    public function fromJson($json, $type)
    {
        $phpType = is_object($type) ? new DefaultPhpType(get_class($type)) : new DefaultPhpType($type);
        $typeAdapter = $this->typeAdapterProvider->getAdapter($phpType);
        $instance = $typeAdapter->readFromJson($json);

        if (is_string($type)) {
            return $instance;
        }

        $properties = $this->propertyCollectionFactory->create($phpType);

        /** @var Property $property */
        foreach ($properties as $property) {
            $property->set($type, $property->get($instance));
        }

        return $type;
    }

    /**
     * Converts an object to a [@see JsonElement]
     *
     * This is a convenience method that first converts an object to json utilizing all of the
     * type adapters, then converts that json to a JsonElement.  From here you can modify the
     * JsonElement and call json_encode() on it to get json.
     *
     * @param mixed $object
     * @return JsonElement
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     */
    public function toJsonElement($object)
    {
        return $this->fromJson($this->toJson($object), JsonElement::class);
    }
}
