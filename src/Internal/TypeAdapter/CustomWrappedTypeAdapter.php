<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

declare(strict_types=1);

namespace Tebru\Gson\Internal\TypeAdapter;

use Tebru\Gson\Internal\DefaultJsonDeserializationContext;
use Tebru\Gson\Internal\DefaultJsonSerializationContext;
use Tebru\Gson\JsonWritable;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\JsonDeserializer;
use Tebru\Gson\JsonReadable;
use Tebru\Gson\JsonSerializer;
use Tebru\Gson\TypeAdapter;
use Tebru\Gson\TypeAdapterFactory;
use Tebru\PhpType\TypeToken;

/**
 * Class CustomWrappedTypeAdapter
 *
 * Wraps a [@see JsonSerializer] or [@see JsonDeserializer] and delegates if either is null
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class CustomWrappedTypeAdapter extends TypeAdapter
{
    /**
     * @var TypeToken
     */
    private $type;

    /**
     * @var TypeAdapterProvider
     */
    private $typeAdapterProvider;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var JsonDeserializer
     */
    private $deserializer;

    /**
     * @var TypeAdapterFactory
     */
    private $skip;

    /**
     * Cached instance of the delegate type adapter
     *
     * @var TypeAdapter
     */
    private $delegateTypeAdapter;

    /**
     * Constructor
     *
     * @param TypeToken $type
     * @param TypeAdapterProvider $typeAdapterProvider
     * @param JsonSerializer|null $serializer
     * @param JsonDeserializer|null $deserializer
     * @param TypeAdapterFactory|null $skip
     */
    public function __construct(
        TypeToken $type,
        TypeAdapterProvider $typeAdapterProvider,
        JsonSerializer $serializer = null,
        JsonDeserializer $deserializer = null,
        TypeAdapterFactory $skip = null
    ) {
        $this->type = $type;
        $this->typeAdapterProvider = $typeAdapterProvider;
        $this->serializer = $serializer;
        $this->deserializer = $deserializer;
        $this->skip = $skip;
    }

    /**
     * Read the next value, convert it to its type and return it
     *
     * @param JsonReadable $reader
     * @return mixed
     */
    public function read(JsonReadable $reader)
    {
        if (null === $this->deserializer) {
            $this->delegateTypeAdapter = $this->delegateTypeAdapter ?? $this->typeAdapterProvider->getAdapter($this->type, $this->skip);

            return $this->delegateTypeAdapter->read($reader);
        }

        $jsonElementTypeAdapter = new JsonElementTypeAdapter();
        $jsonElement = $jsonElementTypeAdapter->read($reader);

        return $this->deserializer->deserialize(
            $jsonElement,
            $this->type,
            new DefaultJsonDeserializationContext($this->typeAdapterProvider, $reader->getContext())
        );
    }

    /**
     * Write the value to the writer for the type
     *
     * @param JsonWritable $writer
     * @param mixed $value
     * @return void
     */
    public function write(JsonWritable $writer, $value): void
    {
        if (null === $this->serializer) {
            $this->delegateTypeAdapter = $this->delegateTypeAdapter ?? $this->typeAdapterProvider->getAdapter($this->type, $this->skip);

            $this->delegateTypeAdapter->write($writer, $value);

            return;
        }

        if (null === $value) {
            $writer->writeNull();

            return;
        }

        $jsonElement = $this->serializer->serialize(
            $value,
            $this->type,
            new DefaultJsonSerializationContext($this->typeAdapterProvider, $writer->isSerializeNull())
        );

        $jsonElementTypeAdapter = new JsonElementTypeAdapter();
        $jsonElementTypeAdapter->write($writer, $jsonElement);
    }
}
