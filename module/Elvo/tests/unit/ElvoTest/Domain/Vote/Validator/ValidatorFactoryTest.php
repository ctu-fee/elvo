<?php

namespace ElvoTest\Domain\Vote\Validator;

use Elvo\Domain\Vote\Validator\ValidatorFactory;


class ValidatorFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ValidatorFactory
     */
    protected $validator;


    public function setUp()
    {
        $this->validator = new ValidatorFactory();
    }


    public function testCreateValidatorWithMissingValidatorOption()
    {
        $this->setExpectedException('Elvo\Util\Exception\MissingOptionException', "Missing option 'validator'");
        
        $this->validator->createValidator(array());
    }


    public function testCreateValidator()
    {
        $className = 'FooClass';
        $options = array(
            'foo' => 'bar'
        );
        $validator = $this->getMock('Elvo\Domain\Vote\Validator\ValidatorInterface');
        
        $validatorFactory = $this->getMockBuilder('Elvo\Domain\Vote\Validator\ValidatorFactory')
            ->setMethods(array(
            'createInstance'
        ))
            ->getMock();
        
        $validatorFactory->expects($this->once())
            ->method('createInstance')
            ->with($className, $options)
            ->will($this->returnValue($validator));
        
        $this->assertSame($validator, $validatorFactory->createValidator(array(
            'validator' => $className,
            'options' => $options
        )));
    }


    public function testCreateChainValidatorWithInvalidData()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Validator\Exception\InvalidValidatorDataException');
        
        $this->validator->createChainValidator(array(
            'invalid' => 'data'
        ));
    }


    public function testCreateChainValidator()
    {
        $validatorConfig = array(
            'foo' => 'bar'
        );
        
        $validator1 = $this->getMock('Elvo\Domain\Vote\Validator\ValidatorInterface');
        $validator2 = $this->getMock('Elvo\Domain\Vote\Validator\ValidatorInterface');
        
        $validatorFactory = $this->getMockBuilder('Elvo\Domain\Vote\Validator\ValidatorFactory')
            ->setMethods(array(
            'createValidator'
        ))
            ->getMock();
        
        $validatorFactory->expects($this->once())
            ->method('createValidator')
            ->with($validatorConfig)
            ->will($this->returnValue($validator2));
        
        $chainValidator = $validatorFactory->createChainValidator(array(
            $validator1,
            $validatorConfig
        ));
        
        $this->assertEquals(array(
            $validator1,
            $validator2
        ), $chainValidator->getValidators());
    }
}