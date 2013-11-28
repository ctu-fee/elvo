<?php

namespace Elvo\Console\Command;

use Elvo\Domain\Vote;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Json\Json;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Entity\VoterRole;


class VoteExportCommand extends Command
{

    /**
     * @var Vote\Service\ServiceInterface
     */
    protected $voteService;


    /**
     * @return Vote\Service\ServiceInterface
     */
    public function getVoteService()
    {
        return $this->voteService;
    }


    /**
     * @param Vote\Service\ServiceInterface $voteService
     */
    public function setVoteService(Vote\Service\ServiceInterface $voteService)
    {
        $this->voteService = $voteService;
    }


    protected function configure()
    {
        $this->setName('vote:export')->setDescription('Exports all the votes');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $votes = $this->getVoteService()->fetchAllVotes();
        
        $data = array();
        foreach ($votes as $vote) {
            /* @var $vote \Elvo\Domain\Entity\Vote */
            $candidates = $vote->getCandidates();
            $candidatesData = array();
            foreach ($candidates as $candidate) {
                $candidatesData[] = sprintf("%s %s", $candidate->getFirstName(), $candidate->getLastname());
            }
            
            $data[] = array(
                'role' => $vote->getVoterRole()->getValue(),
                'candidates' => $candidatesData
            );
        }
        
        $jsonData = Json::encode($data);
        $output->writeln($jsonData);
    }
}