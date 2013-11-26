<?php

namespace ElvoTest\Domain\Entity\Factory;

use Elvo\Domain\Entity\VoterRole;
use Elvo\Domain\Entity\Factory\VoterFactory;


class VoterFactoryTest extends \PHPUnit_Framework_Testcase
{

    /**
     * @var VoterFactory
     */
    protected $factory;


    public function setUp()
    {
        $this->factory = new VoterFactory();
    }


    public function testCreateVoterWithInvalidRole()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Exception\InvalidVoterRoleException');
        $this->factory->createVoter('123', 'foo');
    }


    public function testCreateVoteWithRoleAsString()
    {
        $voter = $this->factory->createVoter('123', 'student');
        $this->assertInstanceOf('Elvo\Domain\Entity\Voter', $voter);
        $this->assertSame('123', $voter->getId());
        $this->assertSame('student', $voter->getVoterRole()
            ->getValue());
    }


    public function testCreateVoteWithRoleAsObject()
    {
        $voterRole = VoterRole::student();
        $voter = $this->factory->createVoter('123', $voterRole);
        $this->assertInstanceOf('Elvo\Domain\Entity\Voter', $voter);
        $this->assertSame('123', $voter->getId());
        $this->assertSame($voterRole, $voter->getVoterRole());
    }
}