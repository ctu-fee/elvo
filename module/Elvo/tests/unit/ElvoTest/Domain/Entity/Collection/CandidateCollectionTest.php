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


    public function testFindByIdNotFound()
    {
        $this->collection->append($this->getCandidateMock(123));
        $this->collection->append($this->getCandidateMock(456));
        
        $this->assertNull($this->collection->findById(789));
    }


    public function testFindById()
    {
        $this->collection->append($this->getCandidateMock(123));
        $candidate = $this->getCandidateMock(456);
        $this->collection->append($candidate);
        $this->collection->append($this->getCandidateMock(789));
        
        $this->assertSame($candidate, $this->collection->findById(456));
    }
    
    /*
     * 
     */
    protected function getCandidateMock($id = null)
    {
        $candidate = $this->getMockBuilder('Elvo\Domain\Entity\Candidate')->getMock();
        if ($id) {
            $candidate->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($id));
        }
        
        return $candidate;
    }
}