<?php

namespace Elvo\Console\ServiceManager;

use Zend\ServiceManager\Config;
use Elvo\Console\Command;


class ServiceConfig extends Config
{


    public function getFactories()
    {
        return array(
            'Elvo\Console\VoteCountCommand' => function ($sm)
            {
                $command = new Command\VoteCountCommand();
                $command->setVoteService($sm->get('Elvo\Domain\VoteService'));
                
                return $command;
            }
        );
    }
}