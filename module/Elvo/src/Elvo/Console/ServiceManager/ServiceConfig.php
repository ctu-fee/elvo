<?php

namespace Elvo\Console\ServiceManager;

use Zend\ServiceManager\Config;
use Elvo\Console\Command;
use Elvo\Domain;


class ServiceConfig extends Config
{


    public function getFactories()
    {
        return array(
            /* 
             * ----------------------
             * Handler/services/utils
             * ----------------------
             */
            'Elvo\VoteProcessor' => function ($sm)
            {
                return new Domain\Vote\Processor\Processor();
            },
            
            /* 
             * --------
             * Commands
             * --------
             */
            'Elvo\Console\VoteCountCommand' => function ($sm)
            {
                $command = new Command\VoteCountCommand();
                $command->setVoteService($sm->get('Elvo\Domain\VoteService'));
                
                return $command;
            },
            'Elvo\Console\VoteResultCommand' => function ($sm)
            {
                $command = new Command\VoteResultCommand();
                $command->setVoteService($sm->get('Elvo\Domain\VoteService'));
                $command->setVoteProcessor($sm->get('Elvo\VoteProcessor'));
                
                return $command;
            },
            
            'Elvo\Console\VoteExportCommand' => function ($sm)
            {
                $command = new Command\VoteExportCommand();
                $command->setVoteService($sm->get('Elvo\Domain\VoteService'));
                
                return $command;
            }
        );
    }
}