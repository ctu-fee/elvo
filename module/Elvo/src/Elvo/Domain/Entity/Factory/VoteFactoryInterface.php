<?php

namespace Elvo\Domain\Entity\Factory;

use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Entity\Voter;


/**
 * Interface for vote factories.
 */
interface VoteFactoryInterface
{


    /**
     * Creates a vote based on the "voter" entity and the list of candidates.
     * 
     * @param Voter $voter
     * @param CandidateCollection $candidates
     * @return \Elvo\Domain\Entity\Vote
     */
    public function createVote(Voter $voter, CandidateCollection $candidates);
}