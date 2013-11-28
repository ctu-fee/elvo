<?php

namespace Elvo\Console\Command;

use Elvo\Domain\Vote;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;


class VoteResultCommand extends Command
{

    /**
     * @var Vote\Service\ServiceInterface
     */
    protected $voteService;

    /**
     * @var Vote\Processor\ProcessorInterface
     */
    protected $voteProcessor;


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


    /**
     * @return Vote\Processor\ProcessorInterface
     */
    public function getVoteProcessor()
    {
        return $this->voteProcessor;
    }


    /**
     * @param Vote\Processor\ProcessorInterface $voteProcessor
     */
    public function setVoteProcessor(Vote\Processor\ProcessorInterface $voteProcessor)
    {
        $this->voteProcessor = $voteProcessor;
    }


    protected function configure()
    {
        $this->setName('vote:result')->setDescription('Shows current total votes count');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $votes = $this->getVoteService()->fetchAllVotes();
        $resultCollection = $this->getVoteProcessor()->processVotes($votes);
        
        $data = array();
        foreach ($resultCollection as $result) {
            /* @var $result \Elvo\Domain\Entity\CandidateResult */
            
            $candidate = $result->getCandidate();
            $candidateName = sprintf("%s %s", $candidate->getFirstName(), $candidate->getLastName());
            $data[$candidate->getChamber()->getCode()][] = array(
                $candidateName,
                $result->getNumVotes()
            );
        }
        
        $output->writeln('');
        
        foreach ($data as $chamber => $chamberResultData) {
            $this->renderResult($chamber, $chamberResultData, $output);
        }
    }


    protected function renderResult($chamber, array $data, OutputInterface $output)
    {
        $output->writeln(sprintf("<info>%s</info>", $chamber));
        /* @var $table \Symfony\Component\Console\Helper\TableHelper */
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array(
            'candidate',
            'votes'
        ))->setRows($data);
        
        $table->render($output);
        $output->writeln('');
    }
}