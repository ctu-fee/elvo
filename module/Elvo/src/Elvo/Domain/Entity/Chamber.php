<?php

namespace Elvo\Domain\Entity;


/**
 * A value-object holding the chamber type the candidates are nominated for.
 */
class Chamber
{

    const STUDENT = 'student';

    const ACADEMIC = 'academic';

    /**
     * Enumerated valid codes.
     * @var array
     */
    protected $validCodes = array(
        self::STUDENT,
        self::ACADEMIC
    );

    /**
     * Chamber identification code.
     * @var string
     */
    protected $code;


    /**
     * Constructor.
     * 
     * @param string $code
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($code)
    {
        if (! $this->isValid($code)) {
            throw new Exception\InvalidArgumentException(sprintf("Invalid chamber code '%s'", $code));
        }
        $this->code = $code;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode();
    }


    /**
     * Returns the chamber code.
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * Returns true, if the supplied code is valid.
     * 
     * @param string $code
     * @return boolean
     */
    protected function isValid($code)
    {
        return (in_array($code, $this->validCodes));
    }
}