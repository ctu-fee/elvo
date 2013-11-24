<?php

namespace Elvo\Domain\Vote\Validator;

use Elvo\Domain\Entity\Vote;


/**
 * Chains multiple validators into one.
 */
class ChainValidator implements ValidatorInterface
{

    /**
     * @var ValidatorInterface[]
     */
    protected $validators = array();


    /**
     * Constructor.
     * 
     * @param array $validators
     */
    public function __construct(array $validators)
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
    }


    /**
     * Adds a validator to the chain.
     * 
     * @param ValidatorInterface $validator
     */
    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\Validator\ValidatorInterface::validate()
     */
    public function validate(Vote $vote)
    {
        foreach ($this->validators as $validator) {
            /* @var $validator \Elvo\Domain\Vote\Validator\ValidatorInterface */
            $validator->validate($vote);
        }
    }
}