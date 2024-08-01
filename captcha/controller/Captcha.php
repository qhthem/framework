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
namespace qhphp\captcha\controller;
use qhphp\exception\Exception;
use qhphp\captcha\Captcha as Captchaindex;

class Captcha
{
        
    /**
     * 生成并显示随机验证码图片。
     *
     * @return void
     */
    public function captcha_img() {
        $Captchaindex = new Captchaindex();
        $Captchaindex->createCode(); // 生成验证码
        $Captchaindex->generate(); // 显示验证码图片
    }
    
}