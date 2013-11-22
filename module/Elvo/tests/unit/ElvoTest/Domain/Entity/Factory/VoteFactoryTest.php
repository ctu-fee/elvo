<?php

namespace ElvoTest\Domain\Entity\Factory;

use Elvo\Domain\Entity\VoterRole;
use Elvo\Domain\Entity\Voter;
use Elvo\Domain\Entity\Factory\VoteFactory;
use Elvo\Domain\Entity\Collection\CandidateCollection;


class VoteFactoryTest extends \PHPUnit_Framework_Testcase
{


    public function testCreateVote()
    {
        $voterRole = VoterRole::academic();
        $voter = new Voter('12', $voterRole);
        $candidates = new CandidateCollection();
        
        $factory = new VoteFactory();
        $vote = $factory->createVote($voter, $candidates);
        
        $this->assertSame($voterRole, $vote->getVoterRole());
        $this->assertSame($candidates, $vote->getCandidates());
    }
}