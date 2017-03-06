<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\Naming;

use Tebru\Gson\MethodNamingStrategy;

/**
 * Class UpperCaseMethodNamingStrategy
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class UpperCaseMethodNamingStrategy implements MethodNamingStrategy
{
    /**
     * Accepts the PHP class property name and returns an array of the names
     * of acceptable getter methods
     *
     * @param string $propertyName
     * @return array
     */
    public function translateToGetter($propertyName)
    {
        return [
            'get' . ucfirst($propertyName),
            'is' . ucfirst($propertyName),
        ];
    }

    /**
     * Accepts the PHP class property name and returns an array of the names
     * of acceptable setter methods
     *
     * @param string $propertyName
     * @return array
     */
    public function translateToSetter($propertyName)
    {
        return ['set' . ucfirst($propertyName)];
    }
}
