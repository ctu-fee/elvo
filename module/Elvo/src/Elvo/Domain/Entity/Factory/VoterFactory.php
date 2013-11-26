<?php

namespace Elvo\Domain\Entity\Factory;

use Elvo\Domain\Entity\VoterRole;
use Elvo\Domain\Entity\Voter;


/**
 * Creates "Voter" entities.
 */
class VoterFactory
{


    /**
     * Creates a voter entity.
     * 
     * @param mixed $id
     * @param mixed $role
     * @return Voter
     */
    public function createVoter($id, $role)
    {
        if (! $role instanceof VoterRole) {
            $role = new VoterRole($role);
        }
        return new Voter($id, $role);
    }
}