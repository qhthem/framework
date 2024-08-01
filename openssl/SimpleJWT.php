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
 * 一个简单的 JWT 类，用于编码和解码 JSON Web Tokens
 *
 * @author zhaosong
 * @version 1.0.0
 */
class SimpleJWT
{
    private $secret;

    /**
     * 构造函数，初始化 JWT 类
     *
     * @param string $secret 用于签名和验证 JWT 的密钥
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * 编码 JWT
     *
     * @param array $payload JWT 负载数据
     * @return string 编码后的 JWT
     */
    public function encode($payload)
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload))
        ];

        $signing_input = implode('.', $segments);

        $signature = $this->sign($signing_input, $this->secret);

        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    /**
     * 解码 JWT
     *
     * @param string $token 要解码的 JWT
     * @param bool $verify 是否验证 JWT 的签名
     * @return array 解码后的 JWT 负载数据
     * @throws UnexpectedValueException 如果 JWT 解码失败
     */
    public function decode($token, $verify = true)
    {
        $segments = explode('.', $token);

        if (count($segments) != 3) {
            throw new Exception('Wrong number of segments',404);
        }

        list($header_segment, $payload_segment, $signature_segment) = $segments;

        $header = json_decode($this->base64UrlDecode($header_segment));
        $payload = json_decode($this->base64UrlDecode($payload_segment));

        if ($verify) {
            $signing_input = $header_segment . '.' . $payload_segment;
            $signature = $this->base64UrlDecode($signature_segment);

            if (!$this->verify($signing_input, $this->secret, $signature)) {
                throw new Exception('Signature verification failed',404);
            }
        }

        return $payload;
    }

    /**
     * 使用 HMAC-SHA256 签名数据
     *
     * @param string $msg 要签名的数据
     * @param string $key 签名密钥
     * @return string 签名后的数据
     */
    private function sign($msg, $key)
    {
        return hash_hmac('SHA256', $msg, $key, true);
    }

    /**
     * 验证 HMAC-SHA256 签名
     *
     * @param string $msg 要验证的数据
     * @param string $key 签名密钥
     * @param string $signature 要验证的签名
     * @return bool 如果签名验证成功，返回 true，否则返回 false
     */
    private function verify($msg, $key, $signature)
    {
        $expected = hash_hmac('SHA256', $msg, $key, true);

        return hash_equals($signature, $expected);
    }

    /**
     * 对数据进行 Base64 URL 编码
     *
     * @param string $data 要编码的数据
     * @return string 编码后的数据
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * 对 Base64 URL 编码的数据进行解码
     *
     * @param string $data 要解码的数据
     * @return string 解码后的数据
     */
    private function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}