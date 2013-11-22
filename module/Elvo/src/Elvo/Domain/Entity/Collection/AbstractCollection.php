<?php

namespace Elvo\Domain\Entity\Collection;

use Zend\Stdlib\ArrayObject;


/**
 * Abstract base class for all entity collections.
 */
abstract class AbstractCollection implements \IteratorAggregate, \Countable
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
     * Appends an item to the collection.
     * 
     * @param mixed $item
     */
    public function append($item)
    {
        $this->items->append($item);
    }


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