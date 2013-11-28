<?php

namespace Elvo\Domain\Vote\Processor;

use Elvo\Domain\Entity\Collection\VoteCollection;
use Elvo\Domain\Entity\Collection\CandidateResultCollection;


/**
 * Processes (counts) the votes into a result collection.
 */
class Processor implements ProcessorInterface
{


    public function processVotes(VoteCollection $votes, CandidateResultCollection $candidateResultCollection = null)
    {
        if (null === $candidateResultCollection) {
            $candidateResultCollection = new CandidateResultCollection();
        }
        
        foreach ($votes as $vote) {
            /* @var $vote \Elvo\Domain\Entity\Vote */
            $candidates = $vote->getCandidates();
            foreach ($candidates as $candidate) {
                $candidateResultCollection->addVoteToCandidate($candidate);
            }
        }
        
        return $candidateResultCollection;
    }
}