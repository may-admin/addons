<?php
declare(strict_types=1);

namespace mayadmin\addons;

use think\facade\Log;

class Service extends \think\Service
{
    public function register()
    {
        Log::info('may-addons-Service-register');
    }
    
    public function boot()
    {
        Log::info('may-addons-Service-boot');
    }
}