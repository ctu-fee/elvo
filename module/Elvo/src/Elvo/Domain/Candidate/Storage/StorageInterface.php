<?php

namespace Elvo\Domain\Candidate\Storage;


interface StorageInterface
{


    /**
     * Returns all candidates.
     * 
     * @return \Elvo\Domain\Entity\Collection\CandidateCollection
     */
    public function fetchAll();
}