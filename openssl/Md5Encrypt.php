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
namespace qhphp\openssl;
class Md5Encrypt 
{
    const KEY = 'qwertyuioplkjhgfdsazxcvbnm1234567890'; // 默认加密密钥
    const SALT = 'aKtHBf8rbapVBl9VuLrihGAEUfOu8g2F'; // 默认盐值

    /**
     * 加密字符串
     *
     * @param string $str 待加密的字符串
     * @param string $key 加密密钥
     * @param string $salt 盐值
     * @param integer $iterations 迭代次数
     * @return string 加密后的字符串
     */
    public static function encrypt($str, $key = self::KEY, $salt = self::SALT, $iterations = 1) {
        $str = $str . $salt;
        for ($i = 0; $i < $iterations; $i++) {
            $str = md5($str);
        }
        return $str;
    }

    /**
     * 验证加密后的字符串是否与原始字符串匹配
     *
     * @param string $str 待验证的字符串
     * @param string $md5Str 加密后的字符串
     * @param string $key 加密密钥
     * @param string $salt 盐值
     * @param integer $iterations 迭代次数
     * @return boolean 是否匹配
     */
    public static function check($str, $md5Str, $key = self::KEY, $salt = self::SALT, $iterations = 1) {
        $encryptStr = self::encrypt($str, $key, $salt, $iterations);
        return $encryptStr === $md5Str;
    }
}