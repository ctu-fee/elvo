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
     * The encrypted "envelope" key used to encrypt the vote data. 
     * @var string
     */
    protected $key;


    /**
     * Constructor.
     * 
     * @param string $data
     * @param string $key
     */
    public function __construct($data, $key)
    {
        $this->data = $data;
        $this->key = $key;
    }


    /**
     * Returns the encrypted data.
     * 
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Returns the encrypted "envelope" key.
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}