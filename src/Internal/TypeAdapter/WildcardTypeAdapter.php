<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter;

use Tebru\Gson\Exception\UnexpectedJsonTokenException;
use Tebru\Gson\JsonWritable;
use Tebru\Gson\Internal\DefaultPhpType;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\Internal\TypeToken;
use Tebru\Gson\JsonReadable;
use Tebru\Gson\JsonToken;
use Tebru\Gson\TypeAdapter;

/**
 * Class WildcardTypeAdapter
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class WildcardTypeAdapter extends TypeAdapter
{
    /**
     * @var TypeAdapterProvider
     */
    private $typeAdapterProvider;

    /**
     * Constructor
     *
     * @param TypeAdapterProvider $typeAdapterProvider
     */
    public function __construct(TypeAdapterProvider $typeAdapterProvider)
    {
        $this->typeAdapterProvider = $typeAdapterProvider;
    }

    /**
     * Read the next value, convert it to its type and return it
     *
     * @param JsonReadable $reader
     * @return mixed
     * @throws \Tebru\Gson\Exception\UnexpectedJsonTokenException If the token can't be processed
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     */
    public function read(JsonReadable $reader)
    {
        switch ($reader->peek()) {
            case JsonToken::BEGIN_ARRAY:
                $type = new DefaultPhpType(TypeToken::TYPE_ARRAY);
                break;
            case JsonToken::BEGIN_OBJECT:
                $type = new DefaultPhpType(TypeToken::OBJECT);
                break;
            case JsonToken::STRING:
                $type = new DefaultPhpType(TypeToken::STRING);
                break;
            case JsonToken::NAME:
                $type = new DefaultPhpType(TypeToken::STRING);
                break;
            case JsonToken::BOOLEAN:
                $type = new DefaultPhpType(TypeToken::BOOLEAN);
                break;
            case JsonToken::NUMBER:
                $type = new DefaultPhpType(TypeToken::FLOAT);
                break;
            case JsonToken::NULL:
                $type = new DefaultPhpType(TypeToken::NULL);
                break;
            default:
                throw new UnexpectedJsonTokenException(
                    sprintf('Could not parse token "%s"', $reader->peek())
                );
        }

        return $this->typeAdapterProvider->getAdapter($type)->read($reader);
    }

    /**
     * Write the value to the writer for the type
     *
     * @param JsonWritable $writer
     * @param mixed $value
     * @return void
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     */
    public function write(JsonWritable $writer, $value)
    {
        $adapter = $this->typeAdapterProvider->getAdapter(DefaultPhpType::createFromVariable($value));
        $adapter->write($writer, $value);
    }
}
