<?php

namespace Elvo\Domain\Vote;

use Elvo\Util\Options;
use Elvo\Domain\Entity\Chamber;


/**
 * Manages data related to the voting process.
 */
class VoteManager
{

    const OPT_ENABLED = 'enabled';

    const OPT_START_TIME = 'start_time';

    const OPT_END_TIME = 'end_time';

    const OPT_CHAMBER_MAX_VOTES = 'chamber_max_votes';

    const OPT_ELECTORAL_NAME = 'electoral_name';

    const OPT_CONTACT_EMAIL = 'contact_email';

    const STATUS_NOT_STARTED = 'not_started';

    const STATUS_FINISHED = 'finished';

    const STATUS_RUNNING = 'running';

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var unknown
     */
    protected $defaulMaxVotes = 1;


    /**
     * Constructor.
     * 
     * @param Options $options
     */
    public function __construct(Options $options = null)
    {
        if (null === $options) {
            $options = new Options();
        }
        $this->setOptions($options);
    }


    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }


    /**
     * Returns the start time of the voting.
     * 
     * @return \DateTime
     */
    public function getStartTime()
    {
        return new \DateTime($this->options->get(self::OPT_START_TIME));
    }


    /**
     * Returns the end time of the voting.
     * 
     * @return \DateTime
     */
    public function getEndTime()
    {
        return new \DateTime($this->options->get(self::OPT_END_TIME));
    }


    /**
     * Returns true, if voting is enabled.
     * 
     * @return boolean
     */
    public function isVotingEnabled()
    {
        return (bool) $this->options->get(self::OPT_ENABLED);
    }


    /**
     * Returns true if the voting is enabled and current time is between
     * the start time and the end time.
     * 
     * @param \DateTime $currentTime
     * @return boolean
     */
    public function isVotingActive(\DateTime $currentTime = null)
    {
        if (! $this->isVotingEnabled()) {
            return false;
        }
        
        return ($this->getVotingStatus($currentTime) == self::STATUS_RUNNING);
    }


    /**
     * Returns the status of the voting (not started, running, finished).
     * 
     * @param \DateTime $currentTime
     * @return string
     */
    public function getVotingStatus(\DateTime $currentTime = null)
    {
        if (null === $currentTime) {
            $currentTime = new \DateTime();
        }
        
        $startTime = $this->getStartTime();
        $endTime = $this->getEndTime();
        
        if ($currentTime < $startTime) {
            return self::STATUS_NOT_STARTED;
        }
        
        if ($currentTime > $endTime) {
            return self::STATUS_FINISHED;
        }
        
        return self::STATUS_RUNNING;
    }


    /**
     * Returns the maximum allowed votes for a particular chamber.
     * 
     * @param Chamber $chamber
     * @return integer
     */
    public function getMaxVotesForChamber(Chamber $chamber)
    {
        $maxVotes = $this->options->get(self::OPT_CHAMBER_MAX_VOTES);
        if (! isset($maxVotes[$chamber->getCode()])) {
            return $this->defaulMaxVotes;
        }
        
        return intval($maxVotes[$chamber->getCode()]);
    }


    /**
     * Returns the name of the electoral.
     * 
     * @return string
     */
    public function getElectoralName()
    {
        return $this->options->get(self::OPT_ELECTORAL_NAME, '{undefined}');
    }


    /**
     * Returns the contact email for the elections.
     * 
     * @return string
     */
    public function getContactEmail()
    {
        return $this->options->get(self::OPT_CONTACT_EMAIL, '{undefined}');
    }
}