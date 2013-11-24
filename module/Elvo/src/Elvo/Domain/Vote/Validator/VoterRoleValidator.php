<?php

namespace Elvo\Domain\Vote\Validator;

use Elvo\Domain\Entity\VoterRole;
use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\Chamber;


/**
 * Validates that all candidates in the vote have "chamber" corresponding
 * to the voter's role. 
 */
class VoterRoleValidator extends AbstractValidator
{

    /**
     * Maps the voter role to the corresponding chamber.
     * @var array
     */
    protected $voteRoleToChamberMap = array(
        VoterRole::STUDENT => Chamber::STUDENT,
        VoterRole::ACADEMIC => Chamber::ACADEMIC
    );


    /**
     * @return array
     */
    public function getVoteRoleToChamberMap()
    {
        return $this->voteRoleToChamberMap;
    }


    /**
     * @param array $voteRoleToChamberMap
     */
    public function setVoteRoleToChamberMap(array $voteRoleToChamberMap)
    {
        $this->voteRoleToChamberMap = $voteRoleToChamberMap;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\Validator\ValidatorInterface::validate()
     */
    public function validate(Vote $vote)
    {
        $voterRoleValue = (string) $vote->getVoterRole();
        if (! isset($this->voteRoleToChamberMap[$voterRoleValue])) {
            throw new Exception\InvalidVoterRoleException(sprintf("Invalid voter role '%s'", $voterRoleValue));
        }
        
        $expectedChamberValue = $this->voteRoleToChamberMap[$voterRoleValue];
        
        foreach ($vote->getCandidates() as $candidate) {
            $chamberValue = (string) $candidate->getChamber();
            if ($chamberValue !== $expectedChamberValue) {
                throw new Exception\VoterRoleMismatchException(sprintf("Candidate ID '%d' candidates to chamber '%s' which is invalid for voter role '%s'", $candidate->getId(), $chamberValue, $expectedChamberValue));
            }
        }
    }
}