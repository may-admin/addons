<?php
namespace mayadmin\addons\command;

class Lang extends Common
{
    protected $type = 'Lang';
    
    protected function configure()
    {
        parent::configure();
        $this->setName('addons:lang')
             ->setDescription('Custom plugin lang');
    }
}