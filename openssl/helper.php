<?php
// +----------------------------------------------------------------------
// | 加解密助手函数
// +----------------------------------------------------------------------
use qhphp\openssl\Md5Encrypt;
use qhphp\openssl\SimpleJWT;
/**
 * 获取JWT 类的实例。
 *
 * @return iwt 返回类的实例。
 */
if (!function_exists('jwt')) {
    function jwt()
    {
        return new SimpleJWT(C('token_request'));
    }
}

/**
 * 加密字符串
 *
 * @param string $key 待加密的字符串
 * @return string 加密后的字符串
 */
if (!function_exists('md5s')) {
    function md5s($key)
    {
        $encryptStr = Md5Encrypt::encrypt($key);
        return $encryptStr;
    }
}

/**
 * 验证加密后的字符串是否与原始字符串匹配
 *
 * @param string $key 加密密钥
 * @param string $value 待验证的字符串
 * @return boolean 是否匹配
 */
if (!function_exists('md5s_check')) {
    function md5s_check($key, $value)
    {
        $isMatch = Md5Encrypt::check($value, $key);
        return $isMatch;
    }
}