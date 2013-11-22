<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\Candidate;


/**
 * A collection of candidate entities.
 */
class CandidateCollection extends AbstractCollection
{


    /**
     * Appends a candidate entity to the collection.
     * 
     * @param Candidate $candidate
     */
    public function append($candidate)
    {
        if (! $candidate instanceof Candidate) {
            $this->throwInvalidItemException($candidate);
        }
        
        parent::append($candidate);
    }
}