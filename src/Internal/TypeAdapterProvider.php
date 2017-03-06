<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal;

use Doctrine\Common\Cache\CacheProvider;
use InvalidArgumentException;
use Tebru\Gson\Annotation\JsonAdapter;
use Tebru\Gson\Internal\TypeAdapter\CustomWrappedTypeAdapter;
use Tebru\Gson\JsonDeserializer;
use Tebru\Gson\JsonSerializer;
use Tebru\Gson\PhpType;
use Tebru\Gson\TypeAdapter;
use Tebru\Gson\TypeAdapterFactory;

/**
 * Class TypeAdapterProvider
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class TypeAdapterProvider
{
    /**
     * A cache of mapped factories
     *
     * @var CacheProvider
     */
    private $typeAdapterCache;

    /**
     * All registered [@see TypeAdapter]s
     *
     * @var TypeAdapterFactory[]
     */
    private $typeAdapterFactories = [];

    /**
     * @var ConstructorConstructor
     */
    private $constructorConstructor;

    /**
     * Constructor
     *
     * @param array $typeAdapterFactories
     * @param CacheProvider $cache
     * @param ConstructorConstructor $constructorConstructor
     */
    public function __construct(array $typeAdapterFactories, CacheProvider $cache, ConstructorConstructor $constructorConstructor)
    {
        $this->typeAdapterFactories = $typeAdapterFactories;
        $this->typeAdapterCache = $cache;
        $this->constructorConstructor = $constructorConstructor;
    }

    /**
     * Add type adapter directly into cache
     *
     * @param string $type
     * @param TypeAdapter $typeAdapter
     */
    public function addTypeAdapter($type, TypeAdapter $typeAdapter)
    {
        $this->typeAdapterCache->save($type, $typeAdapter);
    }

    /**
     * Creates a key based on the type, and optionally the class that should be skipped.
     * Returns the [@see TypeAdapter] if it has already been created, otherwise loops
     * over all of the factories and finds a type adapter that supports the type.
     *
     * @param PhpType $type
     * @param TypeAdapterFactory $skip
     * @return TypeAdapter
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     */
    public function getAdapter(PhpType $type, TypeAdapterFactory $skip = null)
    {
        $key = $type->getUniqueKey();
        $typeAdapter = $this->typeAdapterCache->fetch($key);
        if (null === $skip && false !== $typeAdapter) {
            return $typeAdapter;
        }

        foreach ($this->typeAdapterFactories as $typeAdapterFactory) {
            if ($typeAdapterFactory === $skip) {
                continue;
            }

            if (!$typeAdapterFactory->supports($type)) {
                continue;
            }

            $adapter = $typeAdapterFactory->create($type, $this);

            // do not save skipped adapters
            if (null === $skip) {
                $this->typeAdapterCache->save($key, $adapter);
            }

            return $adapter;
        }

        throw new InvalidArgumentException(sprintf(
            'The type "%s" could not be handled by any of the registered type adapters',
            (string) $type
        ));
    }

    /**
     * Get a type adapter from a [@see JsonAdapter] annotation
     *
     * The class may be a TypeAdapter, TypeAdapterFactory, JsonSerializer, or JsonDeserializer
     *
     * @param PhpType $type
     * @param JsonAdapter $jsonAdapterAnnotation
     * @return TypeAdapter
     * @throws \InvalidArgumentException if an invalid adapter is found
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     */
    public function getAdapterFromAnnotation(PhpType $type, JsonAdapter $jsonAdapterAnnotation)
    {
        $object = $this->constructorConstructor->get(new DefaultPhpType($jsonAdapterAnnotation->getClass()))->construct();

        if ($object instanceof TypeAdapter) {
            return $object;
        }

        if ($object instanceof TypeAdapterFactory) {
            return $object->create($type, $this);
        }

        if ($object instanceof JsonSerializer && $object instanceof JsonDeserializer) {
            return new CustomWrappedTypeAdapter($type, $this, $object, $object);
        }

        if ($object instanceof JsonSerializer) {
            return new CustomWrappedTypeAdapter($type, $this, $object);
        }

        if ($object instanceof JsonDeserializer) {
            return new CustomWrappedTypeAdapter($type, $this, null, $object);
        }

        throw new InvalidArgumentException(sprintf(
            'The type adapter must be an instance of TypeAdapter, TypeAdapterFactory, JsonSerializer, or JsonDeserializer, but "%s" was found',
            get_class($object)
        ));
    }
}
