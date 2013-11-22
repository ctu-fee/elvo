<?php

namespace Elvo\Domain\Entity;


/**
 * The entity represents the "voter" - the user who is going to vote.
 */
class Voter
{

    /**
     * Voter's anonymous unique ID.
     * @var string
     */
    protected $id;

    /**
     * User's voter role.
     * @var VoterRole
     */
    protected $voterRole;


    /**
     * Constructor.
     * 
     * @param string $id
     * @param VoterRole $voterRole
     */
    public function __construct($id, VoterRole $voterRole)
    {
        $this->setId($id);
        $this->setVoterRole($voterRole);
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
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return VoterRole
     */
    public function getVoterRole()
    {
        return $this->voterRole;
    }


    /**
     * @param VoterRole $voterRole
     */
    public function setVoterRole(VoterRole $voterRole)
    {
        $this->voterRole = $voterRole;
    }
}