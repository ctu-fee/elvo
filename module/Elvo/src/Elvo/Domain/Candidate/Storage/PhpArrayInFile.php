<?php

namespace Elvo\Domain\Candidate\Storage;

use InGeneral\Exception\MissingOptionException;
use Elvo\Util\Options;
use Elvo\Util\Exception\InvalidArgumentException;
use Elvo\Domain\Candidate\Storage\Exception\InvalidFileException;
use Elvo\Domain\Candidate\Service\Exception\InvalidCandidateDataException;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Domain\Entity\Collection\CandidateCollection;


class PhpArrayInFile implements StorageInterface
{

    const OPT_FILE_PATH = 'file_path';

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


    protected function createCandidateCollection(array $candidatesData)
    {
        $collection = new CandidateCollection();
        foreach ($candidatesData as $item) {
            $collection->append($this->getCandidateFactory()
                ->createCandidate($item));
        }
        
        return $collection;
    }


    protected function readCandidatesData()
    {
        $filePath = $this->getFilePath();
        if (! $filePath) {
            throw new MissingOptionException(self::OPT_FILE_PATH);
        }
        
        if (! is_file($filePath)) {
            throw new InvalidFileException(sprintf("Invalid file '%s'", $filePath));
        }
        
        if (! is_readable($filePath)) {
            throw new InvalidFileException(sprintf("Cannot read file '%s'", $filePath));
        }
        
        $candidatesData = require $filePath;
        if (! is_array($candidatesData) || empty($candidatesData)) {
            throw new InvalidCandidateDataException(sprintf("Invalid candidate data in file '%s'", $filePath));
        }
        
        return $candidatesData;
    }


    protected function getFilePath()
    {
        return $this->options->get(self::OPT_FILE_PATH);
    }
}