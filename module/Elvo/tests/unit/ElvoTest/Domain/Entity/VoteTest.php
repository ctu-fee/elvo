<?php

namespace ElvoTest\Domain\Entity;

use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Entity\VoterRole;


class VoteTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructor()
    {
        $voterRole = VoterRole::student();
        $candidates = new CandidateCollection();
        
        $vote = new Vote($voterRole, $candidates);
        
        $this->assertSame($voterRole, $vote->getVoterRole());
        $this->assertSame($candidates, $vote->getCandidates());
    }


    public function testHasVoterRole()
    {
        $vote = new Vote(VoterRole::academic(), $this->getCandidateCollectionMock());
        
        $this->assertTrue($vote->hasVoterRole(VoterRole::academic()));
        $this->assertFalse($vote->hasVoterRole(VoterRole::student()));
        
        $vote->setVoterRole(VoterRole::student());
        
        $this->assertFalse($vote->hasVoterRole(VoterRole::academic()));
        $this->assertTrue($vote->hasVoterRole(VoterRole::student()));
    }


    protected function getCandidateCollectionMock()
    {
        $candidates = $this->getMock('Elvo\Domain\Entity\Collection\CandidateCollection');
        return $candidates;
    }
}