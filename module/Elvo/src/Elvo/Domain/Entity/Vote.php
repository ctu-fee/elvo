<?php

namespace Elvo\Domain\Entity;

use Elvo\Domain\Entity\Collection\CandidateCollection;


/**
 * The "vote" entity represents a single vote.
 */
class Vote
{

    /**
     * Voter role.
     * @var VoterRole
     */
    protected $voterRole;

    /**
     * Selected candidates.
     * @var CandidateCollection
     */
    protected $candidates;


    /**
     * Constructor.
     * 
     * @param VoterRole $voterRole
     * @param CandidateCollection $candidates
     */
    public function __construct(VoterRole $voterRole, CandidateCollection $candidates)
    {
        $this->setVoterRole($voterRole);
        $this->setCandidates($candidates);
    }


    /**
     * @return VoterRole
     */
    public function getVoterRole()
    {
        return $this->voterRole;
    }


    /**
     * @param VoterRole $voterRole
     */
    public function setVoterRole(VoterRole $voterRole)
    {
        $this->voterRole = $voterRole;
    }


    /**
     * @return CandidateCollection
     */
    public function getCandidates()
    {
        return $this->candidates;
    }


    /**
     * @param CandidateCollection $candidates
     */
    public function setCandidates(CandidateCollection $candidates)
    {
        $this->candidates = $candidates;
    }


    /**
     * Returns true, if the vote has the provided voter role.
     * 
     * @param VoterRole $voterRole
     * @return boolean
     */
    public function hasVoterRole(VoterRole $voterRole)
    {
        return ($this->voterRole->getValue() == $voterRole->getValue());
    }
}