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

/**
 * 字符串加密解密类
 * @author zhaosong
 */
class StringAuth
{
    private $ckey_length = 4; // 密钥长度
    private $key; // 主密钥
    private $keya; // 密钥A
    private $keyb; // 密钥B
    private $keyc; // 密钥C
    private $cryptkey; // 加密密钥
    private $key_length; // 密钥长度

    /**
     * 构造函数
     * @param string $key 用户提供的密钥，如果没有提供则使用默认密钥
     * @return void
     * @author zhaosong
     */
    public function __construct($key = '')
    {
        $defaultKey = 'arBDnW9QAtUxg8lCGbX2U8tH7bXdpf58';
        $this->key = md5($key != '' ? $key : $defaultKey);
        $this->keya = md5(substr($this->key, 0, 16));
        $this->keyb = md5(substr($this->key, 16, 16));
    }

    /**
     * 字符串加密或解密
     * @param string $string 需要加密或解密的字符串
     * @param string $operation 操作类型，'ENCODE' 表示加密，'DECODE' 表示解密
     * @param int $expiry 有效期，单位为秒，0 表示永久有效
     * @return string 加密或解密后的字符串
     * @author zhaosong
     */

    public function string_auth($string, $operation = 'ENCODE', $expiry = 0)
    {
        if ($operation == 'DECODE') {
            $this->keyc = substr($string, 0, $this->ckey_length);
        } else {
            $this->keyc = $this->ckey_length? substr(md5(microtime()), -$this->ckey_length) : '';
        }

        $this->cryptkey = $this->keya. md5($this->keya. $this->keyc);
        $this->key_length = strlen($this->cryptkey);

        if ($operation == 'DECODE') {
            $string = base64_decode(strtr(substr($string, $this->ckey_length), '-_', '+/'));
        } else {
            $string = sprintf('%010d', $expiry? $expiry + time() : 0). substr(md5($string. $this->keyb), 0, 16). $string;
        }

        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($this->cryptkey[$i % $this->key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result.= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || intval(substr($result, 0, 10)) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26). $this->keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $this->keyc. rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
        }
    }
}
