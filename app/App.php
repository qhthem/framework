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

namespace qhphp\app;
use qhphp\router\Router;
use qhphp\debug\Debug;
use qhphp\router\Dispatcher;

class App{
    
    /**
     * 配置文件路径
     *
     * 此属性存储路由配置文件的路径。
     *
     * @var string
     */
    public static $path = DIRECTORY_SEPARATOR;
    
    const VERSION = '2.0.3';
    
    /**
     * 运行程序
     *
     * 此方法将处理错误、加载路由配置并注册路由。
     *
     * @return void
     */
    public static function run()
    {
        self::Error();
        self::initialize();
        self::AppRouter();
        // 注册路由
        (new Router());
        (new Dispatcher())->init();
        
        date_default_timezone_set(C('default_timezone'));
    }

     
    /**
     * 获取应用基础目录
     * @access public
     * @return string
     */
    public static function getBasePath(): string
    {
        return ROOT_PATH . 'app' . self::$path;
    }
    

    /**
     * 获取当前应用目录
     * @access public
     * @return string
     */
    public static function getAppPath(): string
    {
        return (new Router())->formatAppnName();
    }
    
    /**
     * 获取应用配置目录
     * @access public
     * @return string
     */
    public static function getConfigPath(): string
    {
        return ROOT_PATH .'app'.self::$path.'common'.self::$path.'config' . self::$path;
    }
    
    /**
     * 获取核心框架目录
     * @access public
     * @return string
     */
    public static function getQhphpPath(): string
    {
        return ROOT_PATH.self::$path.'qhphp'.self::$path;
    }
    
    /**
     * 设置runtime目录
     * @param string $path 定义目录
     */
    public static function getRuntimePath(): string
    {
        return ROOT_PATH .self::$path.'app'.self::$path.'common'.self::$path.'runtime'.self::$path;
    }
    
    /**
     * 设置自定义插件目录
     * @param string $path 定义目录
     */
    public static function getExtendPath(): string
    {
        return ROOT_PATH .self::$path.'extend'.self::$path;
    }
    
    /**
     * 设置自定义主题目录
     * @param string $path 定义目录
     */
    public static function getThemPath(string $app): string
    {
        return app()::getBasePath().$app.self::$path.'service'.self::$path.'App.php';
    }
    
    /**
     * 调试模式设置
     * @access protected
     * @return void
     */
    public static function debugModeInit()
    {
       
    }
    
    /**
     * 获取应用运行时目录
     * @access public
     * @return string
     */
    public static function getRunPath(): string
    {
        return ROOT_PATH.self::$path.'public'.self::$path;
    }
    
    /**
     * 加载应用公共函数文件
     * @author zhaosong
     */
    public static function loadfunctions(): void
    {
        $appPath = self::getBasePath();
        // 检查是否存在公共函数文件
        if (is_file($appPath . 'common.php')) {
            include_once $appPath . 'common.php';
        }
    }
    
    /**
     * 加载框架函数文件
     * @author zhaosong
     */
    public static function loadFrameworkfunctions(): void
    {
        $helpers = [];
        $qhPath = self::getQhphpPath();
        // 检查是否存在框架函数目录
        if (is_dir($qhPath)) {
            // 获取目录下所有子目录
            $helpers = glob($qhPath . '*/');
        }
        // 遍历子目录，加载helper.php文件
        array_map(function ($helper) {
            if (is_file($helper . 'helper.php')) {
                include_once $helper . 'helper.php';
            }
        }, $helpers);
    }
    
    /**
     * 加载额外文件列表
     * @author zhaosong
     */
    public static function loadextra_file_list(): void
    {
        $extra = C('extra_file_list');
        // 检查是否存在额外文件列表配置
        if (!empty($extra)) {
            // 遍历文件列表，加载文件
            array_map(function ($k_extra) {
                if (is_file($k_extra)) {
                    include_once $k_extra;
                }
            }, $extra);
        }
    }
    
    /**
     * 初始化应用
     * @access public
     * @return $this
     */
    public static function initialize(): void
    {
       self::loadfunctions();
       
       self::loadFrameworkfunctions();
       
       self::loadextra_file_list();
    }
     
    /**
     * 打开路由应用路由配置
     *
     * @author zhaosong
     */
    public static function AppRouter()
    {
        $Route_path = C('custom_route_path');
        $rules = Cache()->get('Route');
        if(empty($rules)){
            if (is_file($Route_path)) {
                $data = include_once $Route_path;
                Cache()->set('Route', $data,21600);
            }
        }
    }
    
    /**
     * 加载应用错误配置
     * @access protected
     * @return void
     */
    public static function Error()
    {
       Debug::register();
    }
     
}