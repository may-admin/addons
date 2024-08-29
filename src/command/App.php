<?php
namespace mayadmin\addons\command;

use think\console\command\Make;
use think\console\Input;
use think\console\Output;
use think\facade\Console;
use think\facade\Log;

class App extends Make
{
    protected $type = 'App';
    
    protected function configure(): void
    {
        parent::configure();
        $this->setName('addons:app')
             ->setDescription('Custom plugin app');
    }
    
    protected function getStub(): string
    {
        return '';
    }
    
    protected function execute(Input $input, Output $output): void
    {
        $name = $input->getArgument('name') ?: '';
        
        Console::call('addons:controller', [$name]);
        Console::call('addons:model', [$name]);
        Console::call('addons:view', [$name]);
        Console::call('addons:validate', [$name]);
        Console::call('addons:config', [$name]);
        Console::call('addons:lang', [$name]);
        
        $output->writeln('<info>plugin created successfully.</info>');
    }
}