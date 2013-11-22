<?php

namespace ElvoTest\Domain\Entity\Collection;

use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\Collection\VoteCollection;


class VoteCollectionTest extends \PHPUnit_Framework_Testcase
{

    protected $collection;


    public function setUp()
    {
        $this->collection = new VoteCollection();
    }


    public function testAppend()
    {
        $vote = $this->getVoteMock();
        $this->collection->append($vote);
        
        $data = array();
        foreach ($this->collection as $item) {
            $data[] = $item;
        }
        
        $this->assertEquals($vote, $data[0]);
    }
    
    
    public function testAppendWithInvalidItem()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Collection\Exception\InvalidItemException');
        
        $vote = new \stdClass();
        $this->collection->append($vote);
    }
    
    /*
     * 
     */
    protected function getVoteMock()
    {
        $vote = $this->getMockBuilder('Elvo\Domain\Entity\Vote')
            ->disableOriginalConstructor()
            ->getMock();
        return $vote;
    }
}