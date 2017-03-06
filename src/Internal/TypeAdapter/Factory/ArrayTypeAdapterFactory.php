<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter\Factory;

use stdClass;
use Tebru\Gson\Internal\TypeAdapter\ArrayTypeAdapter;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\PhpType;
use Tebru\Gson\TypeAdapter;
use Tebru\Gson\TypeAdapterFactory;

/**
 * Class ArrayTypeAdapterFactory
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class ArrayTypeAdapterFactory implements TypeAdapterFactory
{
    /**
     * Will be called before ::create() is called.  The current type will be passed
     * in.  Return false if ::create() should not be called.
     *
     * @param PhpType $type
     * @return bool
     */
    public function supports(PhpType $type)
    {
        if ($type->isArray()) {
            return true;
        }

        if ($type->isA(stdClass::class)) {
            return true;
        }

        return false;
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
        return new ArrayTypeAdapter($type, $typeAdapterProvider);
    }
}
