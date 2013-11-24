<?php

namespace ElvoTest\Domain\Vote\Validator;

use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Domain\Entity\Candidate;
use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Entity\VoterRole;
use Elvo\Domain\Vote\Validator\VoterRoleValidator;


class VoterRoleValidatorTest extends \PHPUnit_Framework_Testcase
{

    protected $validator;


    public function setUp()
    {
        $this->validator = new VoterRoleValidator();
    }


    public function testValidateWithInvalidVoterRole()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Validator\Exception\InvalidVoterRoleException');
        
        $vote = new Vote(VoterRole::student(), new CandidateCollection());
        $this->validator->setVoteRoleToChamberMap(array());
        $this->validator->validate($vote);
    }


    public function testValidateWithInvalidVote()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Validator\Exception\VoterRoleMismatchException');
        
        $candidates = $this->getCandidates(array(
            array(
                'id' => 1,
                'chamber' => Chamber::academic()
            ),
            array(
                'id' => 2,
                'chamber' => Chamber::student()
            )
        ));
        
        $voterRole = VoterRole::academic();
        $vote = new Vote($voterRole, $candidates);
        
        $this->validator->validate($vote);
    }


    public function testValidate()
    {
        $candidates = $this->getCandidates(array(
            array(
                'id' => 1,
                'chamber' => Chamber::academic()
            ),
            array(
                'id' => 2,
                'chamber' => Chamber::academic()
            )
        ));
        
        $voterRole = VoterRole::academic();
        $vote = new Vote($voterRole, $candidates);
        
        $this->validator->validate($vote);
        $this->assertTrue(true);
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


    protected function getCandidates(array $data)
    {
        $candidateFactory = new CandidateFactory();
        $candidates = new CandidateCollection();
        
        foreach ($data as $item) {
            $candidates->append($candidateFactory->createCandidate(array(
                'id' => $item['id'],
                'chamber' => $item['chamber']
            )));
        }
        
        return $candidates;
    }
}