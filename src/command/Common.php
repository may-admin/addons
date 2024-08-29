<?php
namespace mayadmin\addons\command;

use think\console\command\Make;

use think\helper\Str;
use think\facade\Log;

class Common extends Make
{
    protected $type;
    
    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $this->type . '.stub';
    }
    
    protected function getNamespace(string $app): string
    {
        return 'addons' . ($app ? '\\' . $app : ''). '\\' . Str::lower($this->type);
    }
    
    protected function getPathName(string $name): string
    {
        $name = str_replace('addons\\', '', $name);
        return $this->app->addons->getAddonsPath() . ltrim(str_replace('\\', '/', $name), '/') . '.php';
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
        return $this->getNamespace($plugin) . '\\' . Str::studly($name);
    }
}