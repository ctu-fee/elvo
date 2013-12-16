<?php

namespace Elvo\Domain\Vote\Validator;

use Elvo\Util\Options;


abstract class AbstractValidator implements ValidatorInterface
{

    /**
     * @var Options
     */
    protected $options;


    /**
     * Constructor.
     * 
     * @param array|Options $options
     */
    public function __construct($options = null)
    {
        if (null === $options) {
            $options = new Options();
        }
        
        if (is_array($options)) {
            $options = new Options($options);
        }
        
        $this->setOptions($options);
    }


    /**
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }


    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }
}