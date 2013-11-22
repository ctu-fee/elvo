<?php

namespace Elvo\Domain\Entity\Factory;

use Elvo\Domain\Entity\Candidate;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\HydratorInterface;


/**
 * Factory for creating "candidate" entities.
 */
class CandidateFactory implements CandidateFactoryInterface
{

    /**
     * @var HydratorInterface
     */
    protected $hydrator;


    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (! $this->hydrator instanceof HydratorInterface) {
            $this->hydrator = new ClassMethods();
        }
        return $this->hydrator;
    }


    /**
     * @param HydratorInterface $hydrator
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Entity\Factory\CandidateFactoryInterface::createCandidate()
     */
    public function createCandidate(array $data)
    {
        $candidate = new Candidate();
        $candidate = $this->getHydrator()->hydrate($data, $candidate);
        return $candidate;
    }
}