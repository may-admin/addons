<?php
namespace mayadmin\addons\command;

class Validate extends Common
{
    protected $type = 'Validate';
    
    protected function configure(): void
    {
        parent::configure();
        $this->setName('addons:validate')
             ->setDescription('Custom plugin Validate');
    }
}