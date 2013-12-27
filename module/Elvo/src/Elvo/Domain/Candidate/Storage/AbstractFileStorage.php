<?php

namespace Elvo\Domain\Candidate\Storage;

use InGeneral\Exception\MissingOptionException;
use Elvo\Domain\Candidate\Storage\Exception\InvalidFileException;
use Elvo\Domain\Candidate\Service\Exception\InvalidCandidateDataException;


abstract class AbstractFileStorage extends AbstractStorage
{

    const OPT_FILE_PATH = 'file_path';


    protected function getFilePath()
    {
        return $this->options->get(self::OPT_FILE_PATH);
    }


    protected function checkFile($filePath = null)
    {
        if (null === $filePath) {
            $filePath = $this->getFilePath();
        }
        
        if (! $filePath) {
            throw new MissingOptionException(self::OPT_FILE_PATH);
        }
        
        if (! is_file($filePath)) {
            throw new InvalidFileException(sprintf("Invalid file '%s'", $filePath));
        }
        
        if (! is_readable($filePath)) {
            throw new InvalidFileException(sprintf("Cannot read file '%s'", $filePath));
        }
    }


    protected function checkCandidateData(array $candidatesData)
    {
        if (empty($candidatesData)) {
            throw new InvalidCandidateDataException(sprintf("Invalid candidate data in file '%s'", $filePath));
        }
    }
}