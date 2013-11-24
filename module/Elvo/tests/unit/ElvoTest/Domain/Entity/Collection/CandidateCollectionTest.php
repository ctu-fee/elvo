<?php

namespace ElvoTest\Domain\Entity\Collection;

use Elvo\Domain\Entity\Collection\CandidateCollection;


class CandidateCollectionTest extends \PHPUnit_Framework_Testcase
{

    /**
     * @var CandidateCollection
     */
    protected $collection;


    public function setUp()
    {
        $this->collection = new CandidateCollection();
    }


    public function testAppend()
    {
        $candidate = $this->getCandidateMock();
        $this->collection->append($candidate);
        $this->assertEquals($candidate, $this->collection->offsetGet(0));
    }


    public function testAppendWithInvalidItem()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Collection\Exception\InvalidItemException');
        
        $candidate = new \stdClass();
        $this->collection->append($candidate);
    }
    
    /*
     * 
     */
    protected function getCandidateMock()
    {
        $candidate = $this->getMockBuilder('Elvo\Domain\Entity\Candidate')->getMock();
        
        return $candidate;
    }
}