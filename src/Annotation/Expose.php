<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Annotation;

/**
 * Class Expose
 *
 * Use this annotation to include serialization or deserialization of a property.  This
 * annotation only works with the flag to require this Expose annotation on the [@see Excluder].
 *
 * @author Nate Brunette <n@tebru.net>
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY", "METHOD"})
 */
class Expose
{
    /**
     * Expose this property during serialization
     *
     * @var bool
     */
    private $serialize = true;

    /**
     * Expose this property during deserialization
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
            $this->serialize = $params['serialize'];
        }

        if (isset($params['deserialize'])) {
            $this->deserialize = $params['deserialize'];
        }
    }

    /**
     * Returns true if the property should be exposed based on the direction (serialize/deserialize)
     *
     * @param bool $serialize
     * @return bool
     */
    public function shouldExpose($serialize)
    {
        return $serialize ? $this->serialize : $this->deserialize;
    }
}
