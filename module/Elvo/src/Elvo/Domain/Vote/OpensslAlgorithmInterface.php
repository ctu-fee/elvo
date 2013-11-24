<?php

namespace Elvo\Domain\Vote;

use Zend\Filter\Encrypt\EncryptionAlgorithmInterface;


interface OpensslAlgorithmInterface extends EncryptionAlgorithmInterface
{


    /**
     * @param string|arrray $key
     */
    public function setPublicKey($key);


    /**
     * @return array
     */
    public function getPublicKey();


    /**
     * @param string|array $key
     * @param string $passphrase
     */
    public function setPrivateKey($key, $passphrase = null);


    /**
     * @return array
     */
    public function getPrivateKey();


    /**
     * @param string|array $key
     */
    public function setEnvelopeKey($key);


    /**
     * @return array
     */
    public function getEnvelopeKey();
}