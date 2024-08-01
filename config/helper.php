<?php
// +----------------------------------------------------------------------
// | 配置助手函数
// +----------------------------------------------------------------------
use qhphp\config\Config;


if (!function_exists('C')) {
    /**
     * 获取和设置配置参数
     * @param string|array $name  参数名
     * @param mixed        $value 参数值
     * @return mixed
     */
    function C($name = '', $value = null)
    {
        Config::init(App()::getConfigPath());
        return empty(Config::getfor($name)) ? $value:Config::getfor($name);
    }
}

if (!function_exists('site')) {
    /**
     * 获取和设置配置参数
     * @param string|array $name  参数名
     * @param mixed        $value 参数值
     * @return mixed
     */
    function site($name = '', $value = 'Config')
    {
        Config::init(App()::getConfigPath());
        return Config::get($name,$value);
    }
}

/**
 * 根据配置文件路径，更新配置文件中的值。
 * 
 * @param string $configfile 配置文件的路径。
 * @throws Exception 如果配置文件不可写，抛出异常。
 * @return bool|int 返回写入的字节数，或者在失败时返回false。
 * @author zhaosong
 */
if (!function_exists('Cset')) {
    function Cset(array $config, string $configfile) {
        if (!is_writable($configfile)) throw new Exception(Lang('文件权限不足'), 404);
        $pattern = $replacement = [];
        // 假设$config是从其他地方传入的配置数组
        foreach ($config as $k => $v) {
            $v = str_replace(array(',', '$',), '', $v);
            $pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
            $replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";
        }
        $str = file_get_contents($configfile);
        $str = preg_replace($pattern, $replacement, $str);
        return file_put_contents($configfile, $str, LOCK_EX);
    }
}

/**
 * 获取配置文件中的值。
 * 
 * @param string $files 配置文件的路径。
 * @return mixed 返回配置值，或者在文件不存在或读取失败时返回false。
 * @author zhaosong
 */
if (!function_exists('Cget')) {
    function Cget(string $files, string $key = '') {
        if (!is_file($files)){
            return false;
        }
        if (@include($files)) {
            $array = include $files;
            // 假设$key是从其他地方传入的键，用于获取具体的配置值
            $keys = explode('.', $key);
            $value = $array;
            foreach ($keys as $k) {
                // 使用isset确保键存在，避免未定义索引的错误
                $value = isset($value[$k]) ? $value[$k] : $value;
            }
            return $value;
        } else {
            return false;
        }
    }
}