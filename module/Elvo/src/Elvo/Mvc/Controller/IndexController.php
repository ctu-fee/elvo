<?php

namespace Elvo\Mvc\Controller;

use Zend\View\Model\ViewModel;
use Elvo\Domain\Entity\Chamber;


class IndexController extends AbstractController
{

    protected $timeFormat = 'd.n.Y H:i';


    public function indexAction()
    {
        $this->getAuthService()->clearIdentity();
        
        $voteManager = $this->getVoteManager();
        
        $view = $this->initView(array(
            'votingActive' => $voteManager->isVotingActive(),
            'startTime' => $voteManager->getStartTime()
                ->format($this->timeFormat),
            'endTime' => $voteManager->getEndTime()
                ->format($this->timeFormat),
            'maxVoteCountStudent' => $voteManager->getMaxVotesForChamber(Chamber::student()),
            'maxVoteCountAcademic' => $voteManager->getMaxVotesForChamber(Chamber::academic()),
            'electoralName' => $voteManager->getElectoralName()
        ));
        
        return $view;
    }


    public function autherrorAction()
    {
        $view = $this->initView();
        return $view;
    }
}