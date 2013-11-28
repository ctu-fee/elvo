<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\CandidateResult;


/**
 * Collection of CandidateResult entities.
 */
class CandidateResultCollection extends AbstractCollection
{


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Entity\Collection\AbstractCollection::validate()
     */
    protected function validate($candidateResult)
    {
        if (! $candidateResult instanceof CandidateResult) {
            $this->throwInvalidItemException($candidateResult);
        }
    }
}