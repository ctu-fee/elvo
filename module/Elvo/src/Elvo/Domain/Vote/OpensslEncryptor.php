<?php

namespace Elvo\Domain\Vote;

use Zend\Filter\Encrypt;
use Zend\Serializer;
use Elvo\Util\Exception\MissingOptionException;
use Elvo\Util\Options;
use Elvo\Domain\Entity\EncryptedVote;
use Elvo\Domain\Entity\Vote;


/**
 * The encryptor uses OpenSSL to encrypt/decrypt votes.
 */
class OpensslEncryptor implements EncryptorInterface
{

    const OPT_PRIVATE_KEY = 'private_key';

    const OPT_CERTIFICATE = 'certificate';

    const OPT_PASSPHRASE = 'passphrase';

    /**
     * Serializer to serialize the vote object.
     * @var Serializer\Adapter\AdapterInterface
     */
    protected $serializer;

    /**
     * Encryption algorithm.
     * @var OpensslAlgorithmInterface
     */
    protected $algorithm;

    /**
     * @var Options
     */
    protected $options;


    /**
     * Constructor.
     * 
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->setOptions($options);
    }


    /**
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }


    /**
     * @return OpensslAlgorithmInterface
     */
    public function getAlgorithm()
    {
        if (! $this->algorithm instanceof OpensslAlgorithmInterface) {
            $this->algorithm = new Encrypt\Openssl();
        }
        return $this->algorithm;
    }


    /**
     * @param Encrypt\EncryptionAlgorithmInterface $algorithm
     */
    public function setAlgorithm(OpensslAlgorithmInterface $algorithm)
    {
        $this->algorithm = $algorithm;
    }


    /**
     * @return Serializer\Adapter\AdapterInterface
     */
    public function getSerializer()
    {
        if (! $this->serializer instanceof Serializer\Adapter\AdapterInterface) {
            $this->serializer = new Serializer\Adapter\PhpSerialize();
        }
        return $this->serializer;
    }


    /**
     * @param Serializer\Adapter\AdapterInterface $serializer
     */
    public function setSerializer(Serializer\Adapter\AdapterInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\EncryptorInterface::encryptVote()
     */
    public function encryptVote(Vote $vote)
    {
        $serializedVote = $this->getSerializer()->serialize($vote);
        
        $algorithm = $this->getAlgorithm();
        $algorithm->setPublicKey($this->getCertificate());
        $encryptedData = $algorithm->encrypt($serializedVote);
        
        return new EncryptedVote($encryptedData, $algorithm->getEnvelopeKey());
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\EncryptorInterface::decryptVote()
     */
    public function decryptVote(EncryptedVote $encryptedVote)
    {
        $algorithm = $this->getAlgorithm();
        $algorithm->setPrivateKey($this->getPrivateKey());
        
        /*
         * The "Zend/Filter/Encrypt/Openssl" class uses the is_file() function
         * to determine, whether the key is a string or a file. But the is_file() function
         * sometimes emits E_WARNING causing the tests to fail. 
         * So as a quick fix that part of code is "muted".
         */
        @ $algorithm->setEnvelopeKey($encryptedVote->getKey());
        
        $decryptedData = $algorithm->decrypt($encryptedVote->getData());
        
        return $this->getSerializer()->unserialize($decryptedData);
    }


    /**
     * Returns the "private_key" option value.
     * 
     * @throws MissingOptionException
     * @return string
     */
    protected function getPrivateKey()
    {
        $key = $this->options->get(self::OPT_PRIVATE_KEY);
        if (null === $key) {
            throw new MissingOptionException(sprintf("Missing option '%s'", self::OPT_PRIVATE_KEY));
        }
        
        return $key;
    }


    /**
     * Returns the "certificate" option value.
     * 
     * @throws MissingOptionException
     * @return string
     */
    protected function getCertificate()
    {
        $cert = $this->options->get(self::OPT_CERTIFICATE);
        if (null === $cert) {
            throw new MissingOptionException(sprintf("Missing option '%s'", self::OPT_CERTIFICATE));
        }
        
        return $cert;
    }
}