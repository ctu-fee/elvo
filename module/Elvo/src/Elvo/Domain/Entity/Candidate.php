<?php

namespace Elvo\Domain\Entity;


/**
 * "Candidate" entity.
 */
class Candidate
{

    /**
     * Unique ID of the candidate.
     * @var integer
     */
    protected $id;

    /**
     * The chamber the candidate is nominated for.
     * @var Chamber
     */
    protected $chamber;

    /**
     * The first name of the candidate.
     * @var string
     */
    protected $firstName;

    /**
     * The last name of the candidate.
     * @var string
     */
    protected $lastName;

    /**
     * The email of the candidate.
     * @var string
     */
    protected $email;

    /**
     * The candidate's personal profile URL.
     * @var string
     */
    protected $profileUrl;

    /**
     * The candidate's election profile URL.
     * @var string
     */
    protected $candidateUrl;


    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return Chamber
     */
    public function getChamber()
    {
        return $this->chamber;
    }


    /**
     * @param Chamber $chamber
     */
    public function setChamber(Chamber $chamber)
    {
        $this->chamber = $chamber;
    }


    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }


    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }


    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }


    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


    /**
     * @return string
     */
    public function getProfileUrl()
    {
        return $this->profileUrl;
    }


    /**
     * @param string $profileUrl
     */
    public function setProfileUrl($profileUrl)
    {
        $this->profileUrl = $profileUrl;
    }


    /**
     * @return string
     */
    public function getCandidateUrl()
    {
        return $this->candidateUrl;
    }


    /**
     * @param string $candidateUrl
     */
    public function setCandidateUrl($candidateUrl)
    {
        $this->candidateUrl = $candidateUrl;
    }
}