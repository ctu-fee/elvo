<?php

namespace Elvo\Domain\Vote;

use Elvo\Util\Options;


/**
 * Manages data related to the voting process.
 */
class VoteManager
{

    const OPT_ENABLED = 'enabled';

    const OPT_START_TIME = 'start_time';

    const OPT_END_TIME = 'end_time';

    /**
     * @var Options
     */
    protected $options;


    /**
     * Constructor.
     * 
     * @param Options $options
     */
    public function __construct(Options $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
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
        
        if (null === $currentTime) {
            $currentTime = new \DateTime();
        }
        
        $startTime = $this->getStartTime();
        $endTime = $this->getEndTime();
        
        if ($currentTime < $startTime || $currentTime > $endTime) {
            return false;
        }
        
        return true;
    }
}