<?php

namespace Elvo\Domain\Entity;


/**
 * Value-object representing the role of the voter:
 * - student
 * - academic
 */
class VoterRole
{

    const STUDENT = 'student';

    const ACADEMIC = 'academic';

    /**
     * Enumeration of valid roles.
     * @var array
     */
    protected $validRoles = array(
        self::STUDENT,
        self::ACADEMIC
    );

    /**
     * @var string
     */
    protected $role;


    /**
     * Constructor.
     * 
     * @param string $role
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($role)
    {
        if (! $this->isValid($role)) {
            throw new Exception\InvalidArgumentException(sprintf("Invalid voter role '%s'", $role));
        }
        
        $this->role = $role;
    }


    /**
     * Creates a "student" voter role value object.
     * 
     * @return VoterRole
     */
    static public function student()
    {
        return new self(self::STUDENT);
    }


    /**
     * Creates an "academic" voter role value object.
     * 
     * @return VoterRole
     */
    static public function academic()
    {
        return new self(self::ACADEMIC);
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->role;
    }


    /**
     * Returns true, if the role is valid.
     * 
     * @param string $role
     * @return boolean
     */
    protected function isValid($role)
    {
        return (in_array($role, $this->validRoles));
    }
}