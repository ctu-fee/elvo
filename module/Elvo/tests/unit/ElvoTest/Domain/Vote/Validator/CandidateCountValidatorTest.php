<?php

namespace ElvoTest\Domain\Vote\Validator;

use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\VoterRole;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Util\Options;
use Elvo\Domain\Vote\Validator\CandidateCountValidator;


class CandidateCountValidatorTest extends \PHPUnit_Framework_Testcase
{

    protected $validator;


    public function setUp()
    {
        $this->validator = new CandidateCountValidator();
    }


    public function testValidateWithMissingChamberCountOption()
    {
        $this->setExpectedException('Elvo\Util\Exception\MissingOptionException');
        
        $vote = $this->getVoteMock();
        $this->validator->validate($vote);
    }


    public function testValidateWithUnspecifiedChamberCount()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Validator\Exception\InvalidVoteException');
        
        $this->validator->setOptions(
            new Options(array(
                CandidateCountValidator::OPT_CHAMBER_COUNT => array()
            )));
        
        $vote = $this->getVote(VoterRole::academic());
        $this->validator->validate($vote);
    }


    public function testValidateWithExceed()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Validator\Exception\CandidateCountExceededException');
        
        $this->validator->setOptions(
            new Options(
                array(
                    CandidateCountValidator::OPT_CHAMBER_COUNT => array(
                        Chamber::ACADEMIC => 2
                    )
                )));
        
        $vote = $this->getVote(VoterRole::academic(), 
            array(
                array(
                    'id' => 1,
                    'chamber' => Chamber::academic()
                ),
                array(
                    'id' => 2,
                    'chamber' => Chamber::academic()
                ),
                array(
                    'id' => 3,
                    'chamber' => Chamber::academic()
                )
            ));
        
        $this->validator->validate($vote);
    }


    public function testValidateEquals()
    {
        $this->validator->setOptions(
            new Options(
                array(
                    CandidateCountValidator::OPT_CHAMBER_COUNT => array(
                        Chamber::ACADEMIC => 3
                    )
                )));
        
        $vote = $this->getVote(VoterRole::academic(), 
            array(
                array(
                    'id' => 1,
                    'chamber' => Chamber::academic()
                ),
                array(
                    'id' => 2,
                    'chamber' => Chamber::academic()
                ),
                array(
                    'id' => 3,
                    'chamber' => Chamber::academic()
                )
            ));
        
        $this->validator->validate($vote);
        $this->assertTrue(true);
    }


    public function testValidateLess()
    {
        $this->validator->setOptions(
            new Options(
                array(
                    CandidateCountValidator::OPT_CHAMBER_COUNT => array(
                        Chamber::ACADEMIC => 3
                    )
                )));
        
        $vote = $this->getVote(VoterRole::academic(), 
            array(
                array(
                    'id' => 1,
                    'chamber' => Chamber::academic()
                )
            ));
        
        $this->validator->validate($vote);
        $this->assertTrue(true);
    }
    
    /*
     * 
     */
    protected function getVote(VoterRole $voterRole, array $candidateData = array())
    {
        $candidates = new CandidateCollection();
        $candidateFactory = new CandidateFactory();
        foreach ($candidateData as $item) {
            $candidates->append(
                $candidateFactory->createCandidate(
                    array(
                        'id' => $item['id'],
                        'chamber' => $item['chamber']
                    )));
        }
        
        $vote = new Vote($voterRole, $candidates);
        return $vote;
    }


    protected function getVoteMock()
    {
        $vote = $this->getMockBuilder('Elvo\Domain\Entity\Vote')
            ->disableOriginalConstructor()
            ->getMock();
        return $vote;
    }
}