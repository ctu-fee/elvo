<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\Candidate;


/**
 * A collection of candidate entities.
 */
class CandidateCollection extends AbstractCollection
{


    /**
     * Finds the candidate with the provided ID.
     * 
     * @param mixed $id
     * @return Candidate|null
     */
    public function findById($id)
    {
        foreach ($this as $candidate) {
            if ($candidate->getId() == $id) {
                return $candidate;
            }
        }
        return null;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Entity\Collection\AbstractCollection::validate()
     */
    protected function validate($candidate)
    {
        if (! $candidate instanceof Candidate) {
            $this->throwInvalidItemException($candidate);
        }
    }
}