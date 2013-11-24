<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\Candidate;


/**
 * A collection of candidate entities.
 */
class CandidateCollection extends AbstractCollection
{


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