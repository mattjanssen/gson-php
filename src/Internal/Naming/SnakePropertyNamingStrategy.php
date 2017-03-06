<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\Naming;

use Tebru\Gson\PropertyNamingStrategy;

/**
 * Class SnakePropertyNamingStrategy
 *
 * Converts camelCase property names to snake_case
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class SnakePropertyNamingStrategy implements PropertyNamingStrategy
{
    /**
     * Accepts the PHP class property name and returns the name that should
     * appear in json
     *
     * @param string $propertyName
     * @return string
     */
    public function translateName($propertyName)
    {
        $snakeCase = [];
        foreach (str_split($propertyName) as $character) {
            $snakeCase[] = ctype_upper($character)
                ? '_' . strtolower($character)
                : strtolower($character);
        }

        return implode($snakeCase);
    }
}
