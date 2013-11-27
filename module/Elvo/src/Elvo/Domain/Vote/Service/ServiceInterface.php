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
     * Returns true, if the voting is active.
     * 
     * @return boolean
     */
    public function isVotingActive();


    /**
     * Returns true, if the voter has already voted.
     *
     * @param Entity\Voter $voter
     * @return boolean
     */
    public function hasAlreadyVoted(Voter $voter);


    /**
     * Returns true if the voter with the provided ID has already votes.
     * 
     * @param mixed $voterId
     */
    public function hasAlreadyVotedById($voterId);


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


    /**
     * Returns the current vote count;
     * 
     * @return integer
     */
    public function countVotes();
}