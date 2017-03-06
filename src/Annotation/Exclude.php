<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Annotation;

/**
 * Class Exclude
 *
 * Use this annotation to exclude serialization or deserialization of a property
 * that would otherwise be included.
 *
 * @author Nate Brunette <n@tebru.net>
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY", "METHOD"})
 */
class Exclude
{
    /**
     * Exclude this property during serialization
     *
     * @var bool
     */
    private $serialize = true;

    /**
     * Exclude this property during deserialization
     *
     * @var bool
     */
    private $deserialize = true;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (isset($params['serialize'])) {
            $this->serialize = (bool) $params['serialize'];
        }

        if (isset($params['deserialize'])) {
            $this->deserialize = (bool) $params['deserialize'];
        }
    }

    /**
     * Returns true if the property should be excluded based on the direction (serialize/deserialize)
     *
     * @param bool $serialize
     * @return bool
     */
    public function shouldExclude($serialize)
    {
        return $serialize ? $this->serialize : $this->deserialize;
    }
}
