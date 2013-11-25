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
        
        $roles = array();
        if (isset($userData[self::FIELD_VOTER_ROLES]) && is_array($userData[self::FIELD_VOTER_ROLES])) {
            $roles = $userData[self::FIELD_VOTER_ROLES];
        }
        
        return new Identity($id, $roles);
    }
}