<?php

namespace Elvo\Domain\Candidate\Storage;

use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Util\Options;
use Elvo\Util\Exception\InvalidArgumentException;
use Elvo\Domain\Entity\Collection\CandidateCollection;


abstract class AbstractStorage implements StorageInterface
{

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var CandidateFactory
     */
    protected $candidateFactory;


    /**
     * Constructor.
     *
     * @param Options|array $options
     */
    public function __construct($options)
    {
        $this->setOptions($options);
    }


    /**
     * @param Options|array $options
     */
    public function setOptions($options)
    {
        if (is_array($options)) {
            $options = new Options($options);
        }
        
        if (! $options instanceof Options) {
            throw new InvalidArgumentException('The options argument should be array or instance of Elvo\Util\Options');
        }
        
        $this->options = $options;
    }


    /**
     * @return CandidateFactory
     */
    public function getCandidateFactory()
    {
        if (! $this->candidateFactory instanceof CandidateFactory) {
            $this->candidateFactory = new CandidateFactory();
        }
        return $this->candidateFactory;
    }


    /**
     * @param CandidateFactory $candidateFactory
     */
    public function setCandidateFactory(CandidateFactory $candidateFactory)
    {
        $this->candidateFactory = $candidateFactory;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Candidate\Storage\StorageInterface::fetchAll()
     */
    public function fetchAll()
    {
        $candidatesData = $this->readCandidatesData();
        
        try {
            $candidateCollection = $this->createCandidateCollection($candidatesData);
        } catch (\Exception $e) {
            throw new Exception\InvalidCandidateDataException(sprintf("Error creating candidate collection: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
        
        return $candidateCollection;
    }


    /**
     * Returns raw candidates data from the storage.
     * 
     * @return array
     */
    abstract protected function readCandidatesData();


    /**
     * Creates a candidate collection from provided data.
     * 
     * @param array $candidatesData
     * @return CandidateCollection
     */
    protected function createCandidateCollection(array $candidatesData)
    {
        $collection = new CandidateCollection();
        foreach ($candidatesData as $item) {
            $collection->append($this->getCandidateFactory()
                ->createCandidate($item));
        }
        
        return $collection;
    }
}