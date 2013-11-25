<?php

namespace Elvo\Domain\Entity\Factory;

use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Entity\Candidate;


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
        if (isset($data['chamber']) && (! $data['chamber'] instanceof Chamber)) {
            $data['chamber'] = new Chamber($data['chamber']);
        }
        
        $candidate = new Candidate();
        $candidate = $this->getHydrator()->hydrate($data, $candidate);
        return $candidate;
    }
}