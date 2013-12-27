<?php

namespace Elvo\Domain\Candidate\Storage;

use Zend\Json\Json;


class JsonInFile extends AbstractFileStorage
{


    protected function readCandidatesData()
    {
        $filePath = $this->getFilePath();
        $this->checkFile($filePath);
        
        $jsonData = file_get_contents($filePath);
        if (false === $jsonData) {
            throw new Exception\InvalidCandidateDataException(sprintf("Error reading file '%s'", $filePath));
        }
        
        try {
            $candidatesData = Json::decode($jsonData, Json::TYPE_ARRAY);
        } catch (\Exception $e) {
            throw new Exception\InvalidCandidateDataException(sprintf("Error decoding JSON from file '%s'", $filePath), null, $e);
        }
        
        $this->checkCandidateData($candidatesData);
        
        return $candidatesData;
    }
}