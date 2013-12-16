<?php

namespace Elvo\Domain\Entity;


/**
 * Holds the vote count for a single candidate.
 */
class CandidateResult
{

    /**
     * @var Candidate
     */
    protected $candidate;

    /**
     * @var integer
     */
    protected $numVotes;


    /**
     * Constructor.
     * 
     * @param Candidate $candidate
     * @param intefer $numVotes
     */
    public function __construct(Candidate $candidate, $numVotes = 0)
    {
        $this->candidate = $candidate;
        $this->numVotes = $numVotes;
    }


    /**
     * @return Candidate
     */
    public function getCandidate()
    {
        return $this->candidate;
    }


    /**
     * @return integer
     */
    public function getNumVotes()
    {
        return $this->numVotes;
    }


    /**
     * Adds provided amount to the vote count.
     * 
     * @param integer $numVotes
     */
    public function addVotes($numVotes)
    {
        $this->numVotes += $numVotes;
    }


    /**
     * Adds one vote to the vote count.
     */
    public function addOne()
    {
        $this->addVotes(1);
    }
}