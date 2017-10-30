<?php
/**
 * Created by PhpStorm.
 * User: savioresende
 * Date: 2017-10-08
 * Time: 4:43 PM
 */

namespace WTGear\Repositories\Collections;

class BookCollection implements \WTGear\Repositories\Interfaces\CollectionInterface, \Iterator, \ArrayAccess
{
    private $position = 0;

    protected $collection = [];

    public function __construct() {
        $this->position = 0;
    }

    // Iterator Interface ---

    public function current (){
        return $this->collection[$this->position];
    }

    public function key () {
        return $this->position;
    }

    public function next () {
        ++$this->position;
    }

    public function rewind () {
        $this->position = 0;
    }

    public function valid () {
        return isset($this->collection[$this->position]);
    }

    // ArrayAccess Interface ---

    public function offsetExists ( $offset ) {
        return isset($this->collection[$offset]);
    }

    public function offsetGet ( $offset ) {
        return $this->collection[$offset];
    }

    public function offsetSet ( $offset, $value ) {
        $this->collection[$offset] = $value;
    }

    public function offsetUnset ( $offset ) {
        unset($this->collection[$offset]);
    }

    /**
     * Load in the current object the Traversable input (array)
     *
     * @param Traversable|array
     * @return void
     */
    public function loadTraversable( $traversable ){
        $this->collection = $traversable;
    }

}