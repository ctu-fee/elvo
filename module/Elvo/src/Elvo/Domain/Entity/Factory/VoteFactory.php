<?php

namespace Elvo\Domain\Entity\Factory;

use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\Voter;
use Elvo\Domain\Entity\Collection\CandidateCollection;


/**
 * Default implementation of the vote factory interface.
 */
class VoteFactory implements VoteFactoryInterface
{


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Entity\Factory\VoteFactoryInterface::createVote()
     */
    public function createVote(Voter $voter, CandidateCollection $candidates)
    {
        return new Vote($voter->getVoterRole(), $candidates);
    }
}