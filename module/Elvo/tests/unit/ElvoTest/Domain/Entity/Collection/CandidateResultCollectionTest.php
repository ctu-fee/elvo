<?php

namespace ElvoTest\Domain\Entity\Collection;

use Elvo\Domain\Entity\Collection\CandidateCollection;


class CandidateResultCollectionTest extends \PHPUnit_Framework_TestCase
{

    protected $collection;


    public function setUp()
    {
        $this->collection = new CandidateCollection();
    }
    
    
    public function testAppendWithInvalidItem()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Collection\Exception\InvalidItemException');
        
        $item = new \stdClass();
        $this->collection->append($item);
    }
}