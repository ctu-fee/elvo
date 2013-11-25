<?php

namespace Elvo\Mvc\Authentication;


/**
 * User's identity.
 */
class Identity
{

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


    /**
     * Constructor.
     * 
     * @param unknown $id
     * @param array $roles
     */
    public function __construct($id, array $roles = array())
    {
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
     * @param string $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }


    /**
     * @param array $roles
     */
    protected function setRoles($roles)
    {
        $this->roles = $roles;
    }
}