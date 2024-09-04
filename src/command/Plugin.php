<?php
namespace mayadmin\addons\command;

use think\console\Input;
use think\console\Output;
use think\helper\Str;

class Plugin extends Common
{
    protected $type = 'Plugin';
    
    protected function configure(): void
    {
        parent::configure();
        $this->setName('addons:plugin')
             ->setDescription('Custom plugin ini');
    }
    
    protected function getNamespace(string $app): string
    {
        return 'addons' . ($app ? '\\' . $app : '');
    }
    
    protected function getClassName(string $name): string
    {
        if (str_contains($name, '\\')) {
            return $name;
        }
        $plugin = $name;
        if (strpos($name, '@')) {
            [$plugin, $name] = explode('@', $name);
        } else {
            $name = 'Index';
        }
        if (str_contains($plugin, '/')) {
            $plugin = str_replace('/', '\\', $plugin);
        }
        return $this->getNamespace($plugin) . '\\' . $this->type;
    }
    
    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        $path = $this->app->addons->getAddonsPath().$name.'/';
        if (strpos($path, '@')) {
            $path = explode('@', $path)[0].'/';
        }
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        if (!file_exists($path.'config.php')) {
            $plugin_config = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $this->type . '_config.php.stub';
            file_put_contents($path.'config.php', file_get_contents($plugin_config));
        }
        
        if (!file_exists($path.'plugin.ini')) {
            $plugin_ini = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $this->type . '_plugin.ini.stub';
            file_put_contents($path.'plugin.ini', str_replace('{%name%}', $name, file_get_contents($plugin_ini)));
        }
        
        $classname = $this->getClassName($name);
        $pathname = $this->getPathName($classname);
        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ':' . $classname . ' already exists!</error>');
            return false;
        }
        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }
        file_put_contents($pathname, $this->buildClass($classname));
        $output->writeln('<info>' . $this->type . ':' . $classname . ' created successfully.</info>');
    }
}