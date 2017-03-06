<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal;

use stdClass;
use Tebru\Gson\Exception\MalformedTypeException;
use Tebru\Gson\PhpType;

/**
 * Class PhpType
 *
 * Wrapper around core php types and custom types.  It can be as simply as
 *
 *     new PhpType('string');
 *
 * To create a string type.
 *
 * This class also allows us to fake generic types.  The syntax to
 * represent generics uses angle brackets <>.
 *
 * For example:
 *
 *     array<int>
 *
 * Would represent an array of ints.
 *
 *     array<string, int>
 *
 * Would represent an array using string keys and int values.
 *
 * They can be combined, like so
 *
 *     array<string, array<int>>
 *
 * To represent a array with string keys and an array of ints as values.
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class DefaultPhpType implements PhpType
{
    /**
     * The initial type
     *
     * @var string
     */
    private $fullType;

    /**
     * An enum representing core php types
     *
     * @var string
     */
    private $type;

    /**
     * If the type is an object, this will be the object's class name
     *
     * @var string
     */
    private $class;

    /**
     * An array of interfaces that a class implements
     *
     * @var array
     */
    private $interfaces = [];

    /**
     * Generic types, if they exist
     *
     * @var array
     */
    private $genericTypes = [];

    /**
     * Various options a type might need to reference
     *
     * For example, a DateTime object might want to store formatting options
     *
     * @var array
     */
    private $options = [];

    /**
     * Constructor
     *
     * @param string $type
     * @param array $options
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     */
    public function __construct($type, array $options = [])
    {
        $this->options = $options;
        $this->fullType = (string) str_replace(' ', '', $type);

        $this->parseType($this->fullType);
    }

    /**
     * Create a new instance from a variable
     *
     * @param mixed $variable
     * @return PhpType
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     */
    public static function createFromVariable($variable)
    {
        return is_object($variable) ? new self(get_class($variable)) : new self(gettype($variable));
    }

    /**
     * Recursively parse type.  If generics are found, this will create
     * new PhpTypes.
     *
     * @param string $type
     * @return void
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     */
    private function parseType($type)
    {
        if (false === strpos($type, '<')) {
            $this->setType($type);

            return;
        }

        // get start and end positions of generic
        $start = strpos($type, '<');
        $end = strrpos($type, '>');

        if (false === $end) {
            throw new MalformedTypeException('Could not find ending ">" for generic type');
        }

        // get generic types
        $genericTypes = substr($type, $start + 1, $end - $start - 1);

        // set the main type
        $this->setType(substr($type, 0, $start));

        // iterate over subtype to determine if format is <type> or <key, type>
        $depth = 0;
        $type = '';
        foreach (str_split($genericTypes) as $char) {
            // stepping into another generic type
            if ('<' === $char) {
                $depth++;
            }

            // stepping out of generic type
            if ('>' === $char) {
                $depth--;
            }

            // we only care about commas for the initial list of generics
            if (',' === $char && 0 === $depth) {
                // add new type to list
                $this->genericTypes[] = new DefaultPhpType($type);

                // reset type
                $type = '';

                continue;
            }

            // write character key
            $type .= $char;
        }

        $this->genericTypes[] = new DefaultPhpType($type);
    }

    /**
     * Create a type enum and set the class if necessary
     *
     * @param string $type
     * @return void
     */
    private function setType($type)
    {
        $this->type = TypeToken::normalizeType($type);

        if ($this->isObject()) {
            $this->class = TypeToken::OBJECT === $type ? stdClass::class : $type;

            if (class_exists($type)) {
                $this->interfaces = class_implements($this->class);
            }
        } elseif (false === strpos($this->fullType, '<')) {
            $this->fullType = (string) $this->type;
        }
    }

    /**
     * Returns an array of generic types
     *
     * @return array
     */
    public function getGenerics()
    {
        return $this->genericTypes;
    }

    /**
     * Returns the class if an object, or the type as a string
     *
     * @return string
     */
    public function getType()
    {
        return $this->isObject() ? $this->class : $this->fullType;
    }

    /**
     * Returns true if the type matches the class, parent, full type, or one of the interfaces
     *
     * @param string $type
     * @return bool
     */
    public function isA($type)
    {
        $currentType = $this->getType();
        if ($currentType === $type) {
            return true;
        }

        if (in_array($type, $this->interfaces, true)) {
            return true;
        }

        if (is_subclass_of($currentType, $type)) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if this is a string
     *
     * @return bool
     */
    public function isString()
    {
        return $this->type === TypeToken::STRING;
    }

    /**
     * Returns true if this is an integer
     *
     * @return bool
     */
    public function isInteger()
    {
        return $this->type === TypeToken::INTEGER;
    }

    /**
     * Returns true if this is a float
     *
     * @return bool
     */
    public function isFloat()
    {
        return $this->type === TypeToken::FLOAT;
    }

    /**
     * Returns true if this is a boolean
     *
     * @return bool
     */
    public function isBoolean()
    {
        return $this->type === TypeToken::BOOLEAN;
    }

    /**
     * Returns true if this is an array
     *
     * @return bool
     */
    public function isArray()
    {
        return $this->type === TypeToken::TYPE_ARRAY;
    }

    /**
     * Returns true if this is an object
     *
     * @return bool
     */
    public function isObject()
    {
        return $this->type === TypeToken::OBJECT;
    }

    /**
     * Returns true if this is null
     *
     * @return bool
     */
    public function isNull()
    {
        return $this->type === TypeToken::NULL;
    }

    /**
     * Returns true if this is a resource
     *
     * @return bool
     */
    public function isResource()
    {
        return $this->type === TypeToken::RESOURCE;
    }

    /**
     * Returns true if the type could be anything
     *
     * @return bool
     */
    public function isWildcard()
    {
        return $this->type === TypeToken::WILDCARD;
    }

    /**
     * Returns an array of extra options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns a unique identifying key for this type based on
     * the full type and options
     *
     * @return string
     */
    public function getUniqueKey()
    {
        return [] === $this->options
            ? $this->fullType
            : $this->fullType.serialize($this->options);
    }

    /**
     * Return the initial type including generics
     *
     * @return string
     */
    public function __toString()
    {
        return $this->fullType;
    }
}
