<?php
namespace mayadmin\addons\command;

class Model extends Common
{
    protected $type = 'Model';
    
    protected function configure(): void
    {
        parent::configure();
        $this->setName('addons:model')
             ->setDescription('Custom plugin model');
    }
}