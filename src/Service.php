<?php
declare(strict_types=1);

namespace mayadmin\addons;

use think\Console;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Log;

class Service extends \think\Service
{
    // addons 路径
    protected $addons_path;
    
    public function register()
    {
        $this->app->bind('addons', Service::class);
        
        // 无则创建addons目录
        $this->addons_path = $this->getAddonsPath();
        // 加载系统语言包
        $this->loadLang();
        // 自动载入插件
        $this->autoload();
        
        Log::info('may-addons-Service-register');
    }
    
    public function boot()
    {
        Log::info('may-addons-Service-boot');
    }
    
    /**
     * @Description: todo(初始化插件目录)
     * @author 苏晓信 <654108442@qq.com>
     * @date 2024年08月23日
     * @throws
     */
    public function getAddonsPath()
    {
        $addons_path = $this->app->getRootPath().'addons'.DIRECTORY_SEPARATOR;
        if (!is_dir($addons_path)) {
            @mkdir($addons_path, 0755, true);
        }
        return $addons_path;
    }
    
    /**
     * @Description: todo(自动载入插件语言包)
     * @author 苏晓信 <654108442@qq.com>
     * @date 2024年08月23日
     * @throws
     */
    private function loadLang()
    {
        Lang::load([
            $this->app->getRootPath() . '/vendor/may-admin/addons/src/lang/zh-cn.php',
        ]);
    }
    
    /**
     * @Description: todo(自动载入钩子插件)
     * @author 苏晓信 <654108442@qq.com>
     * @date 2024年08月23日
     * @throws
     */
    private function autoload()
    {
        // 是否处理自动载入
        if (Config::get('addons.autoload', true)) {
            $config = Config::get('addons');
            // 读取插件目录及钩子列表
            $base = get_class_methods('\\mayadmin\\addons\\Addons');
            $base = array_merge($base, ['init', 'initialize', 'install', 'uninstall', 'enabled', 'disabled']);
            
            Console::starting(function (Console $console){
                $console->addCommand('mayadmin\addons\command\App', 'addons:app');
                $console->addCommand('mayadmin\addons\command\Controller', 'addons:controller');
                $console->addCommand('mayadmin\addons\command\Model', 'addons:model');
                $console->addCommand('mayadmin\addons\command\View', 'addons:view');
                $console->addCommand('mayadmin\addons\command\Validate', 'addons:validate');
                $console->addCommand('mayadmin\addons\command\Config', 'addons:config');
                $console->addCommand('mayadmin\addons\command\Lang', 'addons:lang');
            });
            
            //dump($base);
            return;
            
            // 读取插件目录中的php文件
            foreach (glob($this->getAddonsPath() . '*/*.php') as $addons_file) {
                // 格式化路径信息
                $info = pathinfo($addons_file);
                // 获取插件目录名
                $name = pathinfo($info['dirname'], PATHINFO_FILENAME);
                // 找到插件入口文件
                if (Str::lower($info['filename']) === 'plugin') {
                    // 读取出所有公共方法
                    if (!class_exists('\\addons\\' . $name . '\\' . $info['filename'])) {
                        continue;
                    }
                    $methods = get_class_methods('\\addons\\' . $name . '\\' . $info['filename']);
                    $ini     = $info['dirname'] . DS . 'plugin.ini';

                    if (!is_file($ini)) {
                        continue;
                    }
                    $addon_config = parse_ini_file($ini, true, INI_SCANNER_TYPED) ?: [];

                    $this->addons_data[]                                  = $addon_config['name'];
                    $this->addons_data_list[$addon_config['name']]        = $addon_config;
                    $this->addons_data_list_config[$addon_config['name']] = include $this->getAddonsPath() . $addon_config['name'] . '/config.php';
                    // 跟插件基类方法做比对，得到差异结果
                    setAddonConfig($config, $methods, $base, $name);
                }
            }
            //插件配置信息保存到缓存
            Cache::set('addons_config', $config);
            //插件列表
            Cache::set('addons_data', $this->addons_data);
            //插件ini列表
            Cache::set('addons_data_list', $this->addons_data_list);
            //插件config列表
            Cache::set('addons_data_list_config', $this->addons_data_list_config);
            Config::set($config, 'addons');
        }
    }
}