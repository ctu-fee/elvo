<?php

namespace Elvo\Util;

use Zend\Stdlib\ArrayObject;


class Options extends ArrayObject
{


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