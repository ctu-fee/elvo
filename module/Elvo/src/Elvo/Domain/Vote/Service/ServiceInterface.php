<?php

namespace Elvo\Domain\Vote\Service;

use Elvo\Domain\Entity\Voter;
use Elvo\Domain\Entity\Collection\CandidateCollection;


/**
 * Vote service interface.
 */
interface ServiceInterface
{


    /**
     * Creates and saves a vote.
     * 
     * @param Voter $voter The identity which is voting.
     * @param CandidateCollection $candidates The list of candidates to ve voted.
     */
    public function saveVote(Voter $voter, CandidateCollection $candidates);


    /**
     * Fetches all votes.
     * 
     * @return \Elvo\Domain\Entity\Collection\VoteCollection
     */
    public function fetchAllVotes();
}