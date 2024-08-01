<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] 配置读取类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace qhphp\config;
use qhphp\app\App;
use Exception;

class Config
{
    // 配置文件路径
    private static $configPath;

    /**
     * 初始化配置类。
     * 
     * @param string $configPath 配置文件的路径。
     * @author zhaosong
     */
    public static function init(string $configPath)
    {
        self::$configPath = $configPath;
    }

    /**
     * 获取指定配置项的值。
     * 
     * @param string $key 配置项的键名。
     * @return mixed 返回配置项的值，如果键名不存在则返回null。
     * @throws Exception 如果参数为空。
     * @author zhaosong
     */
    public static function getFor(string $key):mixed
    {
        if (empty($key)) {
            throw new Exception('The parameter cannot be empty!', 404);
        }

        // 获取配置文件列表
        $files = glob(self::$configPath . '/*.php');

        // 遍历文件列表
        foreach ($files as $file) {
            // 获取文件名
            $fileName = basename($file);
            // 获取文件信息
            $fileInfo = pathinfo($fileName);
            // 获取不带扩展名的文件名
            $fileNamesion = $fileInfo['filename'];

            // 尝试获取配置项
            $has = self::get($key, $fileNamesion);
            if ($has !== null) {
                return $has;
            }
        }

        // 如果没有找到配置项，返回null
        return null;
    }

    /**
     * 检查指定配置文件是否存在。
     * 
     * @param string $file 配置文件的名称（不包括扩展名）。
     * @return bool 如果配置文件存在且可读，则返回true；否则返回false。
     * @author zhaosong
     */
    public static function has(string $file):bool
    {
        $filePath = self::$configPath . $file . '.php';
        if (is_file($filePath) && is_readable($filePath)) {
            return true;
        }
        return false;
    }

    /**
     * 获取配置文件中的配置项。
     * 
     * @param string $key 配置项的键名，默认为空，表示返回整个配置数组。
     * @param string $file 配置文件的名称（不包括扩展名），默认为'app'。
     * @return mixed 如果$key为空，返回配置数组；否则返回指定的配置项值。
     * @author zhaosong
     */
    public static function get(string $key = '', string $file = 'app'):mixed
    {
        // 检查配置文件是否存在
        if (!self::has($file)) {
            return null;
        }

        // 包含配置文件并获取配置数组
        $configArray = include self::$configPath . $file . '.php';

        // 如果$key为空，返回整个配置数组
        if (empty($key)) {
            return $configArray;
        }

        // 解析键名并获取配置项的值
        $keys = explode('.', $key);
        $value = $configArray;
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * 设置配置项的值。
     * 
     * @param array $config 要设置的配置项及其值的数组。
     * @param string $file 配置文件的名称（不包括扩展名），默认为'Config'。
     * @throws Exception 如果配置文件不可写。
     * @author zhaosong
     */
    public static function set(array $config, string $file = 'Config')
    {
        // 配置文件的路径
        $configFile = self::$configPath. $file. '.php';
    
        // 检查配置文件是否可写
        if (!is_writable($configFile)) {
            throw new Exception('Insufficient file permissions', 404);
        }
    
        // 读取配置文件内容
        $str = file_get_contents($configFile);
    
        // 获取原始配置数据
        $originalConfig = include $configFile;
        
        // 处理前端未提交的数据，将其值设为空
        foreach ($originalConfig as $key => $value) {
            if (!array_key_exists($key, $config)) {
                $config[$key] = '';
            }
        }
    
        // 准备替换配置项的正则表达式和替换字符串
        foreach ($config as $k => $v) {
            
            // 清理值中的特殊字符
            $v = str_replace(array(',', '$',), '', "{$v}");
    
            // 构建正则表达式和替换字符串
            $pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
            $replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";
        }
    
        // 使用正则表达式替换配置项的值
        $str = preg_replace($pattern, $replacement, $str);
    
        // 写入配置文件
        file_put_contents($configFile, $str, LOCK_EX);
    }

    /**
     * 为所有配置文件设置配置项的值。
     * 
     * @param array $config 要设置的配置项及其值的数组。
     * @author zhaosong
     */
    public static function setFor(array $config)
    {
        // 获取配置文件列表
        $files = glob(self::$configPath . '/*');

        // 遍历文件列表
        foreach ($files as $file) {
            // 获取文件名
            $fileName = basename($file);
            // 获取文件信息
            $fileInfo = pathinfo($fileName);
            // 获取不带扩展名的文件名
            $fileNamesion = $fileInfo['filename'];

            // 为当前配置文件设置配置项的值
            self::set($config, $fileNamesion);
        }
    }
}