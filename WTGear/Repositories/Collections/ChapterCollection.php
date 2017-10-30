<?php
/**
 * Chapter Collection Repository
 *
 * @author Savio Resende <savio@savioresende,c
 */

namespace WTGear\Repositories\Collections;

class ChapterCollection implements \WTGear\Repositories\Interfaces\CollectionInterface, \Iterator, \ArrayAccess
{
    protected $collection = [];
    private $position = 0;

    public function __construct()
    {
        $this->position = 0;
    }

    // Iterator Interface ---

    public function current()
    {
        return $this->collection[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->collection[$this->position]);
    }

    // ArrayAccess Interface ---

    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    public function offsetGet($offset)
    {
        if( isset($this->collection[$offset]) )
            return $this->collection[$offset];

        return false;
    }

    public function offsetSet($offset, $value)
    {
        $this->collection[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    /**
     * Load in the current object the Traversable input (array)
     *
     * @param Traversable|array
     * @return void
     */
    public function loadTraversable($traversable)
    {
        // transfer array of \WP_Post to array of \Repositories\Entities\Book
        $traversable = array_map(function ($item) {
            $item_array = (array) $item;

            $chapter_entity = new \Repositories\Entities\Chapter();

            foreach ($item_array as $key => $item_attribute){
                $chapter_entity->{strtolower($key)} = $item_attribute;
            }

            return $chapter_entity;
        }, $traversable);

        $this->collection = $traversable;
    }

}