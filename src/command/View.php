<?php
namespace mayadmin\addons\command;

use think\helper\Str;

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
        $name_arr = explode('\\', $name);
        $name_arr[count($name_arr)-1] = Str::snake($name_arr[count($name_arr)-1]);
        $name = implode('/', $name_arr).'/';
        
        return $this->app->addons->getAddonsPath() . $name . 'index.html';
    }
}