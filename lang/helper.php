<?php
// +----------------------------------------------------------------------
// | 语言包函数
// +----------------------------------------------------------------------
use qhphp\lang\Lang;

/**
 * 判断Lang函数是否已经定义，如果没有定义，则定义一个Lang函数
 *
 * @param string $key 语言词条的键名
 * @param string $langPath 语言包文件夹路径，默认为空字符串
 * @return string 返回对应的语言词条
 */
if (!function_exists('Lang')) {
    function Lang($key, $langPath = '')
    {
        $lang = new Lang();
        $langPaths = $langPath ? $langPath : app()::getQhphpPath() . 'lang' . DIRECTORY_SEPARATOR;
        $lang->load($langPaths);
        return $lang->get($key);
    }
}
