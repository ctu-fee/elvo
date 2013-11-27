<?php

namespace Elvo\Mvc\Authentication;

use ZfcShib\Authentication\Identity\IdentityFactoryInterface;


/**
 * Creates the application specific user identity object.
 */
class IdentityFactory implements IdentityFactoryInterface
{

    const FIELD_VOTER_ID = 'voter_id';

    const FIELD_VOTER_ROLES = 'voter_roles';

    const ROLE_STUDENT = 'student';

    const ROLE_ACADEMIC = 'academic';


    /**
     * {@inheritdoc}
     * @see \ZfcShib\Authentication\Identity\IdentityFactoryInterface::createIdentity()
     */
    public function createIdentity(array $userData)
    {
        if (! isset($userData[self::FIELD_VOTER_ID]) || ! $userData[self::FIELD_VOTER_ID]) {
            throw new Exception\MissingUniqueIdException(sprintf("Missing '%s' in user data", self::FIELD_VOTER_ID));
        }
        
        $id = $userData[self::FIELD_VOTER_ID];
        
        if (! isset($userData[self::FIELD_VOTER_ROLES])) {
            throw new Exception\MissingRoleException(sprintf("Missing '%s' in user data", self::FIELD_VOTER_ROLES));
        }
        
        $roles = $this->decodeRoles($userData[self::FIELD_VOTER_ROLES]);
        if (empty($roles)) {
            throw new Exception\InvalidRoleException(sprintf("No roles decoded from value '%s'", $userData[self::FIELD_VOTER_ROLES]));
        }
        
        return new Identity($id, $roles);
    }


    /**
     * Decodes roles value to an array of roles.
     * 
     * @param mixed $encodedRoles
     * @return array
     */
    public function decodeRoles($encodedRoles)
    {
        $roles = array();
        
        // If the value is greater than 7, there is a problem
        if ($encodedRoles < 0 || $encodedRoles > 7) {
            return $roles;
        }
        
        $binValue = strrev(sprintf("%03s", decbin($encodedRoles)));
        
        if ($binValue[0]) {
            $roles[] = self::ROLE_STUDENT;
        }
        
        if ($binValue[1] || $binValue[2]) {
            $roles[] = self::ROLE_ACADEMIC;
        }
        
        return $roles;
    }
}