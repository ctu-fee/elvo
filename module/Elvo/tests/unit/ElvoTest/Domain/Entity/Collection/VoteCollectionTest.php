<?php

namespace ElvoTest\Domain\Entity\Collection;

use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\Collection\VoteCollection;
use Elvo\Domain\Entity\VoterRole;


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
        $this->assertEquals($vote, $this->collection->offsetGet(0));
    }


    public function testAppendWithInvalidItem()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Collection\Exception\InvalidItemException');
        
        $vote = new \stdClass();
        $this->collection->append($vote);
    }


    public function testCountByVoterRole()
    {
        $voterRole = VoterRole::academic();
        $this->collection->append($this->getVoteMock($voterRole, true));
        $this->collection->append($this->getVoteMock($voterRole, true));
        $this->collection->append($this->getVoteMock($voterRole, false));
        $this->collection->append($this->getVoteMock($voterRole, true));
        $this->collection->append($this->getVoteMock($voterRole, false));
        
        $this->assertSame(3, $this->collection->countByVoterRole($voterRole));
    }
    
    /*
     * 
     */
    protected function getVoteMock($voterRole = null, $result = null)
    {
        $vote = $this->getMockBuilder('Elvo\Domain\Entity\Vote')
            ->disableOriginalConstructor()
            ->getMock();
        
        if ($voterRole !== null && $result !== null) {
            $vote->expects($this->once())
                ->method('hasVoterRole')
                ->with($voterRole)
                ->will($this->returnValue($result));
        }
        
        return $vote;
    }
}