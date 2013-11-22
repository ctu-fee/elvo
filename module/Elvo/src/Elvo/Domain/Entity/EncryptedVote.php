<?php

namespace Elvo\Domain\Entity;


/**
 * Value-object that holds encrypted vote data (string).
 */
class EncryptedVote
{

    /**
     * Encrypted vote data.
     * @var string
     */
    protected $data;


    /**
     * Constructor.
     * 
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * Returns the encrypted data.
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->data;
    }


    /**
     * Returns the encrypted vote as a string.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}