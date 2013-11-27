<?php

namespace Elvo\Console\Command;

use Elvo\Domain\Vote;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;


class VoteCountCommand extends Command
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
        $this->setName('vote:count')->setDescription('Shows current total votes count');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->getVoteService()->countVotes();
        $output->writeln(sprintf("There are currently %d vote(s).", $count));
    }
}