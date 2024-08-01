<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace qhphp\router;
use Exception;

class Dispatcher
{
    /**
     * 初始化
     * @author zhaosong
     */
    public function init()
    {
        $this->GetNamespace();
    }

    /**
     * 获取命名空间
     * @author zhaosong
     * @return mixed
     * @throws Exception
     */
    public function GetNamespace():mixed
    {
        $appName = $this->appmap(Router()->formatAppnName());
        $className = Router()->formatClassName();
        $actionName = Router()->formatActionName();
        $namespace = "app\\" . strtolower($appName) . "\\controller\\" . ucwords($className);

        if (substr($appName, 0, 5) === "qhphp") {
            return $this->GetBind($appName);
        }

        if (!is_dir(App()::getBasePath() . $appName) || !class_exists($namespace)) {
            $empty_controller = C('empty_controller');
            if(!empty($empty_controller)){
                $namespace = $this->empty_controller($empty_controller);
            }else{
                $message = !is_dir(App()::getBasePath() . $appName) ? "app not exists" : "controller not exists";
                throw new Exception(Lang($message) . ": {$appName} or {$className}", 404);
            }
        }

        $class = new $namespace();

        if (!is_callable([$class, $actionName])) {
            throw new Exception(Lang('method not exists') . ": {$className} -> {$actionName}", 404);
        }

        return $this->GetDispatch($class, $actionName);
    }

    /**
     * 获取绑定
     * @author zhaosong
     * @param $appName
     * @return mixed
     * @throws Exception
     */
    public function GetBind($appName):mixed
    {
        $parts = explode('\\', $appName);
        if (!is_dir(App()::getQhphpPath() . $parts[1])) {
            throw new Exception(Lang('app not exists') . ": {$parts[1]}", 404);
        }

        $namespace = "$parts[0]\\" . strtolower($parts[1]) . "\\$parts[2]\\" . ucwords($parts[3]);
        if (!class_exists($namespace)) {
            throw new Exception(Lang('controller not exists') . ": {$parts[3]}", 404);
        }
        $class = new $namespace();
        if (!is_callable([$class, $parts[4]])) {
            throw new Exception(Lang('method not exists') . ": {$parts[3]} -> {$parts[4]}", 404);
        }

        return $this->GetDispatch($class, $parts[4]);
    }

    /**
     * 应用映射
     * @author zhaosong
     * @param $app
     * @return mixed
     * @throws Exception
     */
    private function appmap($app):mixed
    {
        // 检查应用是否被禁用
        $deny = C('deny_app_list');
        if (!empty($deny)) {
            foreach ($deny as $k_app) {
                if ($k_app == $app) {
                    throw new Exception(Lang('App access prohibited') . ": {$app}", 404);
                }
            }
        }
        // 设置应用映射规则
        $appMap = C('app_map');

        if (isset($appMap)) {
            foreach ($appMap as $k => $v) {
                if ($v == $app) {
                    throw new Exception(Lang('app not exists') . ': ' . $app, 404);
                }
            }
        }

        return empty($appMap[$app]) ? $app : $appMap[$app];
    }
    
    /**
     * 检查并返回控制器类名，如果不存在则抛出异常。
     * 
     * @param string $empty_controller 控制器类名，如果为空，则使用$parts数组中的第四个元素作为控制器类名。
     * @param array $parts 请求的URL部分数组，用于在$empty_controller为空时获取控制器类名。
     * @return string 返回控制器类名。
     * @throws Exception 如果控制器类不存在，则抛出404异常。
     * @author zhaosong
     */
    private function empty_controller($empty_controller)
    {
        if (!empty($empty_controller)) {
            $namespace = $empty_controller;
            // 检查控制器类是否存在
            if (!class_exists($namespace)) {
                // 如果不存在，抛出异常
                throw new Exception(Lang('Empty controller does not exist') . ": {$empty_controller}", 404);
            }
            // 如果存在，返回控制器类名
            return $namespace;
        }
    }

    /**
     * 获取调度
     * @author zhaosong
     * @param $controller
     * @param $actionName
     * @throws Exception
     */
    private function GetDispatch($controller, $actionName)
    {
        // 检查控制器中是否存在指定的方法
        if (method_exists($controller, $actionName)) {
            if (substr($actionName, 0, 1) == '_') {
                // 如果方法以 '_' 开头，则表示该方法不可访问
                throw new Exception(Lang('This action is inaccessible') . '', 404);
            } else {
                call_user_func([$controller, $actionName]);
                if(APP_DEBUG){
					Createdebug()::stop();
					Createdebug()::debugMessage();
				}
            }
        } else {
            // 如果方法不存在，则返回错误信息
            throw new Exception(Lang('method not exists') . ': ' . $actionName, 404);
        }
    }
}