<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal;

use Iterator;

/**
 * Class AbstractIterator
 *
 * @author Nate Brunette <n@tebru.net>
 */
abstract class AbstractIterator implements Iterator
{
    /**
     * Queue of elements to be iterated
     *
     * @var array
     */
    protected $queue = [];

    /**
     * Total number of elements in queue
     *
     * @var int
     */
    protected $total = 0;

    /**
     * Cursor position
     *
     * @var int
     */
    protected $iterated = 0;

    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current()
    {
        return $this->queue[$this->iterated][1];
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
        $this->iterated++;
    }

    /**
     * Return the key of the current element
     *
     * @return string
     */
    public function key()
    {
        return $this->queue[$this->iterated][0];
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->total > $this->iterated;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
        $this->iterated = 0;
    }
}
