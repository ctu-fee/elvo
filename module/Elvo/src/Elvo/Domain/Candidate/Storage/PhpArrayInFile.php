<?php

namespace Elvo\Domain\Candidate\Storage;

use InGeneral\Exception\MissingOptionException;
use Elvo\Util\Options;
use Elvo\Util\Exception\InvalidArgumentException;
use Elvo\Domain\Candidate\Storage\Exception\InvalidFileException;
use Elvo\Domain\Candidate\Service\Exception\InvalidCandidateDataException;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Domain\Entity\Collection\CandidateCollection;


class PhpArrayInFile extends AbstractFileStorage
{


    protected function readCandidatesData()
    {
        $filePath = $this->getFilePath();
        $this->checkFile($filePath);
        
        $candidatesData = require $filePath;
        if (! is_array($candidatesData)) {
            throw new InvalidCandidateDataException(sprintf("Invalid candidate data in file '%s'", $filePath));
        }
        
        $this->checkCandidateData($candidatesData);
        
        return $candidatesData;
    }
}