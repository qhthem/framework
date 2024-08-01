<?php
// +----------------------------------------------------------------------
// | 验证码助手函数
// +----------------------------------------------------------------------
use qhphp\captcha\Captcha;

/**
 * 生成随机验证码图片的URL。
 *
 * @return string 验证码图片的URL。
 */
if (!function_exists('captcha_img')) {
    function captcha_img()
    {
        $src = "/captcha/".rand(1,10);
        return $src;
    }
}

if (!function_exists('captcha_check')){
    /**
     * @param string $value
     * @return bool
     */
    function captcha_check($value)
    {
        $Captcha = new Captcha();
        return $Captcha->check($value);
    }
}
