<?php

namespace Elvo\Console\ServiceManager;

use Zend\ServiceManager\Config;
use Elvo\Console\Application;
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
            
            'Elvo\Console\Application' => function ($sm)
            {
                $application = new Application();
                $application->addCommands(array(
                    $sm->get('Elvo\Console\VoteCountCommand'),
                    $sm->get('Elvo\Console\VoteResultCommand'),
                    $sm->get('Elvo\Console\VoteExportCommand')
                ));
                
                return $application;
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