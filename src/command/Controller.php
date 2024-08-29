<?php
namespace mayadmin\addons\command;

class Controller extends Common
{
    protected $type = 'Controller';
    
    protected function configure(): void
    {
        parent::configure();
        $this->setName('addons:controller')
             ->setDescription('Custom plugin controller');
    }
}