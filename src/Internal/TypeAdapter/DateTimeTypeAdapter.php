<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter;

use DateTime;
use DateTimeZone;
use Tebru\Gson\JsonWritable;
use Tebru\Gson\JsonReadable;
use Tebru\Gson\JsonToken;
use Tebru\Gson\PhpType;
use Tebru\Gson\TypeAdapter;

/**
 * Class DateTimeTypeAdapter
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class DateTimeTypeAdapter extends TypeAdapter
{
    /**
     * @var PhpType
     */
    private $type;

    /**
     * @var string
     */
    private $format;

    /**
     * Constructor
     *
     * @param PhpType $type
     * @param string $format
     */
    public function __construct(PhpType $type, $format)
    {
        $this->type = $type;
        $this->format = $format;
    }

    /**
     * Read the next value, convert it to its type and return it
     *
     * @param JsonReadable $reader
     * @return DateTime|null
     */
    public function read(JsonReadable $reader)
    {
        if ($reader->peek() === JsonToken::NULL) {
            return $reader->nextNull();
        }

        $formattedDateTime = $reader->nextString();
        $format = isset($this->type->getOptions()['format']) ? $this->type->getOptions()['format'] : null;
        $timezone = isset($this->type->getOptions()['timezone'])? $this->type->getOptions()['timezone'] : null;

        if (null === $format) {
            $format = $this->format;
        }

        if (null !== $timezone) {
            $timezone = new DateTimeZone($timezone);
        }

        /** @var DateTime $class */
        $class = $this->type->getType();

        if (null === $timezone) {
            return $class::createFromFormat($format, $formattedDateTime);
        } else {
            return $class::createFromFormat($format, $formattedDateTime, $timezone);
        }
    }

    /**
     * Write the value to the writer for the type
     *
     * @param JsonWritable $writer
     * @param DateTime $value
     * @return void
     */
    public function write(JsonWritable $writer, $value)
    {
        if (null === $value) {
            $writer->writeNull();

            return;
        }

        $format = isset($this->type->getOptions()['format']) ? $this->type->getOptions()['format'] : null;

        if (null === $format) {
            $format = $this->format;
        }

        $dateTime = $value->format($format);
        $writer->writeString($dateTime);
    }
}
