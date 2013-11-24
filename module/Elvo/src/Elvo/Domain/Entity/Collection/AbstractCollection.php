<?php

namespace Elvo\Domain\Entity\Collection;

use Zend\Stdlib\ArrayObject;


/**
 * Abstract base class for all entity collections.
 */
abstract class AbstractCollection implements \IteratorAggregate, \Countable, \ArrayAccess
{

    /**
     * @var \ArrayObject
     */
    protected $items;


    /**
     * Constructor.
     * 
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->items = new ArrayObject();
        foreach ($data as $item) {
            $this->append($item);
        }
    }


    /**
     * Appends an item to the collection.
     *
     * @param mixed $item
     */
    public function append($item)
    {
        $this->validate($item);
        $this->items->append($item);
    }


    /**
     * {@inhertidoc}
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return $this->items->getIterator();
    }


    /**
     * {@inhertidoc}
     * @see Countable::count()
     */
    public function count()
    {
        return $this->items->count();
    }


    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->items->offsetExists($offset);
    }


    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items->offsetGet($offset);
    }


    /**
     * @param mixed $offset
     * @param mixed $item
     */
    public function offsetSet($offset, $item)
    {
        $this->validate($item);
        $this->items->offsetSet($offset, $item);
    }


    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->items->offsetUnset($offset);
    }


    /**
     * Returns the list of candidates as an array.
     * 
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->items->getArrayCopy();
    }


    /**
     * Validates the item, if it can be added to the collection.
     * 
     * @param mixed $item
     * @throws Exception\InvalidItemException
     */
    abstract protected function validate($item);


    /**
     * Throws an exception indicating that the item is invalid for the current collection.
     * 
     * @param mixed $item
     * @throws Exception\InvalidItemException
     */
    protected function throwInvalidItemException($item)
    {
        if (is_object($item)) {
            $type = get_class($item);
        } else {
            $type = gettype($item);
        }
        
        throw new Exception\InvalidItemException(sprintf("Trying to add invalid item of type '%s' to the collection '%s'", $type, get_class($this)));
    }
}