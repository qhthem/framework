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
use Exception;
/**
 * 使用 AES-256-GCM 密码学加密算法的安全加解密类。
 */
class SecureCrypto {
    
    const CIPHER = 'aes-256-gcm';
    const KEY_LENGTH = 32;

    private $key = 'qwertyuioplkjhgfdsazxcvbnm1234567890';

    /**
     * 使用给定的密钥构造一个新的 SecureCrypto 对象。
     *
     * @param string $key 加密密钥，必须至少为 32 字节长。
     *
     * @throws Exception 如果密钥长度少于 32 字节。
     */
    public function __construct() {
        if (strlen($this->key)< self::KEY_LENGTH) {
            throw new Exception('密钥长度必须至少为 ' . self::KEY_LENGTH . ' 字节。',404);
        }

        $this->key = substr($this->key, 0, self::KEY_LENGTH);
    }

    /**
     * 使用 AES-256-GCM 密码学加密算法加密给定的明文。
     *
     * @param string $plaintext 要加密的明文。
     *
     * @return string 加密的密文。
     *
     * @throws Exception 如果加密失败。
     */
    public function encrypt($plaintext) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER));
        $tag = '';
        $ciphertext = openssl_encrypt($plaintext, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv, $tag, '', 16);

        if ($ciphertext === false || $tag === false) {
            throw new Exception(Lang('Encryption failed' ),404);
        }

        return $iv . $ciphertext . $tag;
    }

    /**
     * 使用 AES-256-GCM 密码学加密算法解密给定的密文。
     *
     * @param string $ciphertext 要解密的密文。
     *
     * @return string 解密的明文。
     *
     * @throws Exception 如果解密失败。
     */
    public function decrypt($ciphertext) {
        $iv_length = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($ciphertext, 0, $iv_length);
        $tag = substr($ciphertext, -16);
        $ciphertext = substr($ciphertext, $iv_length, -16);

        $plaintext = openssl_decrypt($ciphertext, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($plaintext === false) {
            throw new Exception(Lang('Decryption failed' ),404);
        }

        return $plaintext;
    }
}