<?php

namespace ElvoTest\Domain\Entity\Collection;

use Elvo\Domain\Entity\Collection\CandidateResultCollection;
use Elvo\Domain\Entity\CandidateResult;


class CandidateResultCollectionTest extends \PHPUnit_Framework_TestCase
{

    protected $collection;


    public function setUp()
    {
        $this->collection = new CandidateResultCollection();
    }


    public function testAppendWithInvalidItem()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Collection\Exception\InvalidItemException');
        
        $item = new \stdClass();
        $this->collection->append($item);
    }


    public function testFindByCandidateId()
    {
        $candidateId = 123;
        $this->assertNull($this->collection->findByCandidateId($candidateId));
        
        $candidateResult = $this->createCandidateResult($this->getCandidateMock($candidateId));
        $this->collection->append($candidateResult);
        
        $this->collection->append($this->createCandidateResult($this->getCandidateMock(456)));
        $this->collection->append($this->createCandidateResult($this->getCandidateMock(789)));
        
        $this->assertSame($candidateResult, $this->collection->findByCandidateId($candidateId));
    }


    public function testFindByCandidate()
    {
        $candidateId = 123;
        $candidate = $this->getCandidateMock($candidateId);
        $candidateResult = $this->getCandidateResultMock();
        
        $collection = $this->getMockBuilder('Elvo\Domain\Entity\Collection\CandidateResultCollection')
            ->setMethods(array(
            'findByCandidateId'
        ))
            ->getMock();
        $collection->expects($this->once())
            ->method('findByCandidateId')
            ->with($candidateId)
            ->will($this->returnValue($candidateResult));
        
        $this->assertSame($candidateResult, $collection->findByCandidate($candidate));
    }


    public function testAddVoteToCandidateWithExistingCandidateResult()
    {
        $candidateId = 123;
        $candidate = $this->getCandidateMock($candidateId);
        
        $candidateResult = $this->getCandidateResultMock();
        $candidateResult->expects($this->once())
            ->method('addOne');
        
        $collection = $this->getCandidateResultCollectionMock($candidate, $candidateResult);
        $collection->addVoteToCandidate($candidate);
    }


    public function testAddVoteToCandidateWithNonExistingCandidateResult()
    {
        $candidateId = 123;
        $candidate = $this->getCandidateMock($candidateId);
        
        $candidateResult = $this->getCandidateResultMock();
        $candidateResult->expects($this->once())
            ->method('addOne');
        
        $collection = $this->getCandidateResultCollectionMock($candidate);
        $collection->expects($this->once())
            ->method('createCandidateResult')
            ->with($candidate)
            ->will($this->returnValue($candidateResult));
        
        $collection->addVoteToCandidate($candidate);
    }
    
    /*
     * 
     */
    protected function getCandidateResultCollectionMock($candidate = null, $candidateResult = null)
    {
        $collection = $this->getMockBuilder('Elvo\Domain\Entity\Collection\CandidateResultCollection')
            ->setMethods(array(
            'createCandidateResult',
            'findByCandidate',
            'findByCandidateId'
        ))
            ->getMock();
        
        if ($candidateResult && $candidate) {
            $collection->expects($this->once())
                ->method('findByCandidate')
                ->with($candidate)
                ->will($this->returnValue($candidateResult));
        }
        
        return $collection;
    }


    protected function createCandidateResult($candidate, $numVotes = 0)
    {
        return new CandidateResult($candidate, $numVotes);
    }


    protected function getCandidateResultMock()
    {
        $candidateResult = $this->getMockBuilder('Elvo\Domain\Entity\CandidateResult')
            ->disableOriginalConstructor()
            ->getMock();
        return $candidateResult;
    }


    protected function getCandidateMock($id)
    {
        $candidate = $this->getMock('Elvo\Domain\Entity\Candidate');
        $candidate->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));
        return $candidate;
    }
}