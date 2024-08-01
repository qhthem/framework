<?php
// +----------------------------------------------------------------------
// | cookie助手函数
// +----------------------------------------------------------------------
use qhphp\cookie\Cookie;
/**
 * 设置一个cookie。
 *
 * @param string $name cookie名称
 * @param string $value cookie值
 * @param int $expire cookie过期时间（单位：秒）
 * @return void
 */
if (!function_exists('cookie')) {
    function cookie($name, $value, $expire = 0)
    {
        $cookie = new Cookie();
        $cookie->set($name, $value, $expire);
    }
}

/**
 * 获取指定名称的cookie值。
 *
 * @param string $name cookie名称
 * @return mixed 返回cookie值，如果不存在则返回null
 */
if (!function_exists('get_cookie')) {
    function get_cookie($name)
    {
        $cookie = new Cookie();
        return $cookie->get($name);
    }
}

/**
 * 删除指定名称的cookie。
 *
 * @param string $name cookie名称
 * @return void
 */
if (!function_exists('del_cookie')) {
    function del_cookie($name)
    {
        $cookie = new Cookie();
        $cookie->delete($name);
    }
}