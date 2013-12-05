<?php

namespace Elvo\Domain\Vote\Validator;

use Elvo\Domain\Vote\VoteManager;
use Elvo\Util\Exception\MissingOptionException;
use Elvo\Util\Exception\UndefinedClassException;
use InGeneral\Factory\ClassWithOptionsGenericFactory;
use Elvo\Util\Options;


class ValidatorFactory extends ClassWithOptionsGenericFactory implements ValidatorFactoryInterface
{

    const OPT_VALIDATOR = 'validator';

    const OPT_OPTIONS = 'options';

    /**
     * @var VoteManager
     */
    protected $voteManager;


    /**
     * @return VoteManager
     */
    public function getVoteManager()
    {
        return $this->voteManager;
    }


    /**
     * @param VoteManager $voteManager
     */
    public function setVoteManager(VoteManager $voteManager)
    {
        $this->voteManager = $voteManager;
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Validator\ValidatorFactoryInterface::createValidator()
     * @throws MissingOptionException
     */
    public function createValidator(array $options)
    {
        if (! isset($options[self::OPT_VALIDATOR])) {
            throw new MissingOptionException(sprintf("Missing option '%s'", self::OPT_VALIDATOR));
        }
        
        $className = $options[self::OPT_VALIDATOR];
        
        $validatorOptions = array();
        if (isset($options[self::OPT_OPTIONS])) {
            $validatorOptions = $options[self::OPT_OPTIONS];
        }
        
        return $this->createInstance($className, $validatorOptions);
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Validator\ValidatorFactoryInterface::createChainValidator()
     */
    public function createChainValidator(array $validators = array())
    {
        $chainValidator = new ChainValidator();
        foreach ($validators as $validator) {
            if (is_array($validator)) {
                $validator = $this->createValidator($validator);
            }
            
            if ($validator instanceof ValidatorInterface) {
                $chainValidator->addValidator($validator);
                continue;
            }
            
            throw new Exception\InvalidValidatorDataException('Invalid validator object or configuration');
        }
        
        return $chainValidator;
    }
}