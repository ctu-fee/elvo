<?php

namespace Elvo\Mvc\ServiceManager;

use Zend\ServiceManager\Config;


class ServiceConfig extends Config
{


    public function getFactories()
    {
        return array(
            'Elvo\Translator' => 'Zend\I18n\Translator\TranslatorServiceFactory'
        );
    }
}