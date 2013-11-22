<?php

namespace Elvo\Domain\Entity\Factory;


/**
 * Interface for candidate factories.
 */
interface CandidateFactoryInterface
{


    /**
     * Creates a "candidate" entity.
     * 
     * @param array $data
     * @return \Elvo\Domain\Entity\Candidate
     */
    public function createCandidate(array $data);
}