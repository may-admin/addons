<?php
namespace mayadmin\addons\command;

class View extends Common
{
    protected $type = 'View';
    
    protected function configure(): void
    {
        parent::configure();
        $this->setName('addons:view')
             ->setDescription('Custom plugin view');
    }
    
    protected function getPathName(string $name): string
    {
        $name = str_replace('addons\\', '', $name);
        return $this->app->addons->getAddonsPath() . ltrim(str_replace('\\', '/', $name), '/') . '.html';
    }
}