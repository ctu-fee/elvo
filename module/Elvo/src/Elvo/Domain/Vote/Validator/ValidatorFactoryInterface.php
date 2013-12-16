<?php

namespace Elvo\Domain\Vote\Validator;


interface ValidatorFactoryInterface
{


    /**
     * Create a vote validator.
     * 
     * @param array $options
     * @return ValidatorInterface
     */
    public function createValidator(array $options);


    /**
     * Creates a chain validator.
     *
     * @param array $validators
     * @throws Exception\InvalidValidatorDataException
     * @return ChainValidator
     */
    public function createChainValidator(array $validators);
}