<?php
declare(strict_types=1);

namespace mayadmin\addons;

use think\exception\HttpException;
use think\facade\Config;
use think\facade\Event;
use think\helper\Str;

class Route
{
    /**
     * 插件路由请求
     * @param null $addon
     * @param null $controller
     * @param null $action
     * @return mixed
     */
    public static function execute($addon = null, $controller = null, $action = null): mixed
    {
        $app = app();
        $request = $app->request;
        
        // Event::trigger('addons_begin', $request);   //还不懂什么意思
        // 是否自动转换控制器和操作名
        $addon = $addon ? trim($addon) : '';
        $controller = $controller ? trim($controller) : $app->route->config('default_controller');
        $action = $action ? trim($action) : $app->route->config('default_action');
        
        if (empty($addon) || empty($controller) || empty($action)) {
            throw new HttpException(500, lang('addon can not be empty'));
        }
        $app->http->name($addon);
        
        $request->addon = $addon;
        // 设置当前请求的控制器、操作
        $request->setController($controller)->setAction($action);
        
        // 获取插件基础信息
        // 这段仅仅用于判断插件是否禁用，先放着
        // $info = get_addons_info($addon);
        // if (!$info) {
        //     throw new HttpException(404, lang('addon %s not found', [$addon]));
        // }
        // if ($info['status'] != 1) {
        //     throw new HttpException(500, lang('addon %s is disabled', [$addon]));
        // }
        // 这段仅仅用于判断插件是否禁用，先放着

        // 监听addon_module_init
        // Event::trigger('addon_module_init', $request);   //还不懂什么意思
        
        $class = get_addons_class($addon, 'controller', $controller);
        
        if (!$class) {
            throw new HttpException(404, lang('addon controller %s not found', [Str::studly($controller)]));
        }
        
        // 重写视图基础路径
        $config = Config::get('view');
        $config['view_path'] = $app->addons->getAddonsPath() . $addon . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
        Config::set($config, 'view');

        // 生成控制器对象
        $instance = new $class($app);
        $vars = [];
        
        if (is_callable([$instance, $action])) {
            // 执行操作方法
            $call = [$instance, $action];
        } elseif (is_callable([$instance, '_empty'])) {
            // 空操作
            $call = [$instance, '_empty'];
            $vars = [$action];
        } else {
            // 操作不存在
            throw new HttpException(404, lang('addon action %s not found', [get_class($instance) . '->' . $action . '()']));
        }
        // Event::trigger('addons_action_begin', $call);

        return call_user_func_array($call, $vars);
    }
}