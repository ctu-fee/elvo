<?php

namespace Elvo\Mvc\Authentication;


/**
 * User's identity.
 */
class Identity
{

    const ROLE_STUDENT = 'student';

    const ROLE_ACADEMIC = 'academic';

    /**
     * Roles valid for the user.
     * @var array
     */
    protected $validRoles = array(
        self::ROLE_STUDENT,
        self::ROLE_ACADEMIC
    );

    /**
     * Unique user ID.
     * @var string
     */
    protected $id;

    /**
     * List of user roles.
     * @var array
     */
    protected $roles = array();

    protected $primaryRole = null;


    /**
     * Constructor.
     * 
     * @param unknown $id
     * @param array $roles
     */
    public function __construct($id, array $roles = array(), array $validRoles = null)
    {
        if (null !== $validRoles) {
            $this->validRoles = $validRoles;
        }
        
        $this->setId($id);
        $this->setRoles($roles);
    }


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }


    /**
     * @return array
     */
    public function getValidRoles()
    {
        return $this->validRoles;
    }


    /**
     * Returns true, if the user has the provided role.
     * 
     * @param string $role
     * @return boolean
     */
    public function hasRole($role)
    {
        return (in_array($role, $this->roles));
    }


    /**
     * Returns true, if the role is valid.
     * 
     * @param string $role
     * @return boolean
     */
    public function isValidRole($role)
    {
        return (in_array($role, $this->validRoles));
    }


    /**
     * Returns true, if the user has multiple roles.
     * 
     * @return boolean
     */
    public function hasMultipleRoles()
    {
        return (count($this->roles) > 1);
    }


    /**
     * Sets the primary role of the user.
     * 
     * @param string $role
     * @throws Exception\InvalidRoleException
     */
    public function setPrimaryRole($role)
    {
        $this->validateRole($role);
        
        if (! $this->hasRole($role)) {
            throw new Exception\InvalidRoleException(sprintf("Cannot set role '%s' as primary, which is not assigned to the user, current user roles: %s", $role, implode(', ', $this->getRoles())));
        }
        
        $this->primaryRole = $role;
    }


    /**
     * @return string
     */
    public function getPrimaryRole()
    {
        return $this->primaryRole;
    }


    /**
     * @param string $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @param array $roles
     */
    protected function setRoles(array $roles)
    {
        $this->roles = array();
        $this->primaryRole = null;
        
        foreach ($roles as $role) {
            $this->validateRole($role);
            $this->roles[] = $role;
        }
        
        if (count($this->roles) == 1) {
            $this->primaryRole = $this->roles[0];
        }
    }


    /**
     * Validates the role and throws an exception if the role is invalid.
     * 
     * @param string $role
     * @throws Exception\InvalidRoleException
     */
    protected function validateRole($role)
    {
        if (! $this->isValidRole($role)) {
            throw new Exception\InvalidRoleException(sprintf("Invalid role '%s', valid roles are: %s", $role, implode(', ', $this->validRoles)));
        }
    }
}