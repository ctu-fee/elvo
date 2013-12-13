<?php

namespace ElvoFuncTest;

use Elvo\Domain\Entity\Collection\CandidateResultCollection;
use Elvo\Domain\Entity\Candidate;


class CandidateResultCollectionTest extends \PHPUnit_Framework_TestCase
{

    protected $collection;


    public function setUp()
    {
        $this->collection = new CandidateResultCollection();
    }


    public function testAddVoteToCandidate()
    {
        $candidate1 = $this->createCandidate(123);
        $candidate2 = $this->createCandidate(456);
        $candidate3 = $this->createCandidate(789);
        
        $this->collection->addVoteToCandidate($candidate1);
        $this->collection->addVoteToCandidate($candidate2);
        $this->collection->addVoteToCandidate($candidate3);
        $this->collection->addVoteToCandidate($candidate1);
        $this->collection->addVoteToCandidate($candidate2);
        $this->collection->addVoteToCandidate($candidate1);
        $this->collection->addVoteToCandidate($candidate2);
        $this->collection->addVoteToCandidate($candidate1);
        $this->collection->addVoteToCandidate($candidate3);
        $this->collection->addVoteToCandidate($candidate1);
        
        $this->assertSame(5, $this->collection->findByCandidate($candidate1)
            ->getNumVotes());
        $this->assertSame(3, $this->collection->findByCandidate($candidate2)
            ->getNumVotes());
        $this->assertSame(2, $this->collection->findByCandidate($candidate3)
            ->getNumVotes());
    }
    
    /*
     * 
     */
    protected function createCandidate($id)
    {
        $candidate = new Candidate();
        $candidate->setId($id);
        
        return $candidate;
    }
}