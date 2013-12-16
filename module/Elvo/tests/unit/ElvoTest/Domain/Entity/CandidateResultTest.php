<?php

namespace ElvoTest\Domain\Entity;

use Elvo\Domain\Entity\CandidateResult;


class CandidateResultTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructorWithImplicitNumVotes()
    {
        $candidate = $this->getCandidateMock();
        $cr = new CandidateResult($candidate);
        
        $this->assertSame(0, $cr->getNumVotes());
        $this->assertSame($candidate, $cr->getCandidate());
    }


    public function testConstructorWithExplicitNumVotes()
    {
        $candidate = $this->getCandidateMock();
        $num = 108;
        $cr = new CandidateResult($candidate, $num);
        
        $this->assertSame($num, $cr->getNumVotes());
        $this->assertSame($candidate, $cr->getCandidate());
    }
    
    /*
     * 
     */
    protected function getCandidateMock()
    {
        $candidate = $this->getMock('Elvo\Domain\Entity\Candidate');
        return $candidate;
    }
}