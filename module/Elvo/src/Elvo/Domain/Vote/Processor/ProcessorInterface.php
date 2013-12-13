<?php

namespace Elvo\Domain\Vote\Processor;

use Elvo\Domain\Entity\Collection\VoteCollection;
use Elvo\Domain\Entity\Collection\CandidateResultCollection;


/**
 * Processes (counts) the votes into a result collection.
 */
interface ProcessorInterface
{


    /**
     * Processes (counts) the votes into a result collection.
     * 
     * @param VoteCollection $votes
     * @param CandidateResultCollection $candidateResultCollection
     */
    public function processVotes(VoteCollection $votes, CandidateResultCollection $candidateResultCollection = null);
}