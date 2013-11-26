<?php

namespace Elvo\Mvc\Controller\Exception;


class ApplicationErrorException extends \RuntimeException
{

    /**
     * @var string
     */
    protected $errorTitle;

    /**
     * @var string
     */
    protected $errorMessage;


    /**
     * Constructor.
     * 
     * @param string $errorTitle
     * @param string $errorMessage
     */
    public function __construct($errorTitle, $errorMessage = '')
    {
        $this->setErrorTitle($errorTitle);
        $this->setErrorMessage($errorMessage);
        
        parent::__construct(sprintf("Application error [%s]: %s", $errorTitle, $errorMessage));
    }


    /**
     * @return string
     */
    public function getErrorTitle()
    {
        return $this->errorTitle;
    }


    /**
     * @param string $errorTitle
     */
    public function setErrorTitle($errorTitle)
    {
        $this->errorTitle = $errorTitle;
    }


    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }


    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }
}