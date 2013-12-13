<?php

namespace Elvo\Util;

use Zend\Stdlib\ArrayObject;


class Options extends ArrayObject
{


    /**
     * Adds items, that are not already set.
     * 
     * @param array $values
     */
    public function addDefaultValues(array $values)
    {
        foreach ($values as $index => $value) {
            if (! $this->get($index)) {
                $this->set($index, $value);
            }
        }
    }


    public function get($index, $default = null)
    {
        if ($this->offsetExists($index)) {
            return $this->offsetGet($index);
        }
        
        if (null !== $default) {
            return $default;
        }
        
        return null;
    }


    public function set($index, $value)
    {
        $this->offsetSet($index, $value);
    }
}