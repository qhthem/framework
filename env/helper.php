<?php
// +----------------------------------------------------------------------
// | 助手类
// +----------------------------------------------------------------------
use qhphp\env\Env;


/**
 * 获取环境变量的值，如果不存在则返回默认值。
 * 支持创建新的环境变量。
 * 支持删除指定的环境变量。
 * 支持添加新的环境变量。
 * 
 * @param string $name 环境变量的名称，默认为空字符串。
 * @param mixed $default 如果环境变量不存在时的默认返回值，默认为null。
 * @param string $type 操作类型，'get'为获取，'create'为创建，'del'为删除，默认为'get'。
 * @param mixed $value 如果是创建操作，需要传入的值，默认为空字符串。
 * @return mixed 返回环境变量的值或者执行相应操作后的结果。
 * @author zhaosong
 */
if (!function_exists('env')) {

    function env(string $name = '', $default = null, string $type = 'get', mixed $value = '')
    {
        $Env = new Env(); // 创建Env类的实例
        if (empty($name) && $type == 'create') {
            // 如果名称为空且操作为创建，则调用create方法创建新的环境变量
            return $Env->create($value);
        } elseif (!empty($name) && $type == 'get') {
            // 如果名称不为空且操作为获取，则读取环境变量的值
            $data = $Env->read($name);
            return !empty($data) ? $data : $default; // 如果读取到的数据不为空，则返回数据，否则返回默认值
        } elseif (!empty($name) && $type == 'del') {
            // 如果名称不为空且操作为删除，则调用delete方法删除指定的环境变量
            return $Env->delete($name); // 注意这里应该是$name而不是$key
        } else {
            // 否则，调用add方法添加新的环境变量
            return $Env->add($value);
        }
    }
}