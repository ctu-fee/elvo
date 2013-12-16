<?php

namespace Elvo\Mvc\Authentication\Role;


interface RoleExtractorInterface
{


    /**
     * Extracts the required roles from provided data.
     * 
     * @param mixed $roleData
     * @return array
     */
    public function extractRoles($roleData);
}