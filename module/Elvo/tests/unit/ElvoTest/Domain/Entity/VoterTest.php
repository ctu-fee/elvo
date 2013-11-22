<?php

namespace ElvoTest\Domain\Entity;

use Elvo\Domain\Entity\Voter;
use Elvo\Domain\Entity\VoterRole;


class VoterTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructor()
    {
        $id = '1234';
        $voterRole = VoterRole::student();
        
        $voter = new Voter($id, $voterRole);
        
        $this->assertSame($id, $voter->getId());
        $this->assertSame($voterRole, $voter->getVoterRole());
    }
}