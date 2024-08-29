<?php
namespace mayadmin\addons\command;

class Config extends Common
{
    protected $type = 'Config';
    
    protected function configure()
    {
        parent::configure();
        $this->setName('addons:config')
             ->setDescription('Custom plugin config');
    }
}