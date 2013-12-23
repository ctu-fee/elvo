<?php

namespace Elvo\Console;

use Elvo\Util\Version;
use Symfony\Component\Console;


class Application extends Console\Application
{


    public function __construct($name = 'Elvo CLI', $version = null)
    {
        if (null === $version) {
            $version = Version::VERSION;
        }
        
        parent::__construct($name, $version);
    }
}