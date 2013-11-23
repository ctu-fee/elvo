<?php

namespace Elvo\Domain\Vote;

use Zend\Filter\Encrypt;
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
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\EncryptorInterface::encryptVote()
     */
    public function encryptVote(Vote $vote)
    {
        $cryptFilter = $this->createOpensslCryptFilter(array(
            //'private' => $this->getPrivateKey(),
            'public' => $this->getCertificate()
        ));
        //_dump($cryptFilter->getEnvelopeKey());
        $vote = serialize($vote);
        $encryptedData = $cryptFilter->encrypt($vote);
        //_dump($cryptFilter->getEnvelopeKey());
        
        return new EncryptedVote($encryptedData, $cryptFilter->getEnvelopeKey());
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\EncryptorInterface::decryptVote()
     */
    public function decryptVote(EncryptedVote $encryptedVote)
    {
       
        $cryptFilter = $this->createOpensslCryptFilter(array(
            'private' => $this->getPrivateKey(),
            'public' => $this->getCertificate(),
           // 'envelope' => $encryptedVote->getKey()
        ));
        $cryptFilter->setEnvelopeKey($encryptedVote->getKey());
        $decryptedData = $cryptFilter->decrypt($encryptedVote->getData());
        _dump(unserialize($decryptedData));
    }


    protected function createOpensslCryptFilter(array $options)
    {
        $filter = new Encrypt\Openssl($options);
        return $filter;
    }


    protected function getPrivateKey()
    {
        $key = $this->options->get(self::OPT_PRIVATE_KEY);
        if (null === $key) {
            throw new MissingOptionException(sprintf("Missing option '%s'", self::OPT_PRIVATE_KEY));
        }
        
        return $key;
    }


    protected function getCertificate()
    {
        $cert = $this->options->get(self::OPT_CERTIFICATE);
        if (null === $cert) {
            throw new MissingOptionException(sprintf("Missing option '%s'", self::OPT_CERTIFICATE));
        }
        
        return $cert;
    }
}