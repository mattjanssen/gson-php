<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter\Factory;

use Tebru\Gson\Internal\TypeAdapter\CustomWrappedTypeAdapter;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\JsonDeserializer;
use Tebru\Gson\JsonSerializer;
use Tebru\Gson\PhpType;
use Tebru\Gson\TypeAdapter;
use Tebru\Gson\TypeAdapterFactory;

/**
 * Class CustomWrappedTypeAdapterFactory
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class CustomWrappedTypeAdapterFactory implements TypeAdapterFactory
{
    /**
     * @var PhpType
     */
    private $type;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var JsonDeserializer
     */
    private $deserializer;

    /**
     * Constructor
     *
     * @param PhpType $type
     * @param JsonSerializer $serializer
     * @param JsonDeserializer $deserializer
     */
    public function __construct(PhpType $type, JsonSerializer $serializer = null, JsonDeserializer $deserializer = null)
    {
        $this->type = $type;
        $this->serializer = $serializer;
        $this->deserializer = $deserializer;
    }

    /**
     * Will be called before ::create() is called.  The current type will be passed
     * in.  Return false if ::create() should not be called.
     *
     * @param PhpType $type
     * @return bool
     */
    public function supports(PhpType $type)
    {
        return $type->isA($this->type->getType());
    }

    /**
     * Accepts the current type and a [@see TypeAdapterProvider] in case another type adapter needs
     * to be fetched during creation.  Should return a new instance of the TypeAdapter.
     *
     * @param PhpType $type
     * @param TypeAdapterProvider $typeAdapterProvider
     * @return TypeAdapter
     */
    public function create(PhpType $type, TypeAdapterProvider $typeAdapterProvider)
    {
        return new CustomWrappedTypeAdapter($type, $typeAdapterProvider, $this->serializer, $this->deserializer, $this);
    }
}
