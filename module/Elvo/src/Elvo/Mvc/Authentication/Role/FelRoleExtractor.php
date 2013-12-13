<?php

namespace Elvo\Mvc\Authentication\Role;

use Elvo\Mvc\Authentication\Identity;


/**
 * Can extract roles as set in the CTU FEE user database.
 */
class FelRoleExtractor implements RoleExtractorInterface
{


    /**
     * {@inhertidoc}
     * @see \Elvo\Mvc\Authentication\Role\RoleExtractorInterface::extractRoles()
     */
    public function extractRoles($roleData)
    {
        $roles = array();
        
        if (! is_scalar($roleData)) {
            return $roles;
        }
        
        $roleCode = intval($roleData);
        
        if ($roleCode < 0 || $roleCode > 7) {
            return $roles;
        }
        
        $binValue = strrev(sprintf("%03s", decbin($roleCode)));
        
        if ($binValue[0]) {
            $roles[] = Identity::ROLE_STUDENT;
        }
        
        if ($binValue[1] || $binValue[2]) {
            $roles[] = Identity::ROLE_ACADEMIC;
        }
        
        return $roles;
    }
}