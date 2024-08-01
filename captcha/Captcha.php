<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] 验证码类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\captcha;
use qhphp\config\Config;

class Captcha {
    
    /**
     * 验证码字符集合
     *
     * @var string
     */
    protected $codeSet = '';

    /**
     * 验证码过期时间（单位：秒）
     *
     * @var int
     */
    protected $expire;

    /**
     * 使用算术验证码
     *
     * @var bool
     */
    protected $math;

    /**
     * 使用中文验证码
     *
     * @var bool
     */
    protected $useZh = false;

    /**
     * 中文验证码字符串
     *
     * @var string
     */
    protected $zhSet = '';

    /**
     * 使用背景图片
     *
     * @var bool
     */
    protected $useImgBg;

    /**
     * 验证码字体大小（单位：像素）
     *
     * @var int
     */
    protected $fontSize;

    /**
     * 是否画混淆曲线
     *
     * @var bool
     */
    protected $useCurve;

    /**
     * 是否添加杂点
     *
     * @var bool
     */
    protected $useNoise;

    /**
     * 验证码图片高度，设置为0为自动计算
     *
     * @var int
     */
    protected $imageH = 0;

    /**
     * 验证码图片宽度，设置为0为自动计算
     *
     * @var int
     */
    protected $imageW = 0;

    /**
     * 验证码位数
     *
     * @var int
     */
    protected $length;

    /**
     * 验证码字体，不设置则随机获取
     *
     * @var string
     */
    protected $fontttf;

    /**
     * 背景颜色
     *
     * @var array
     */
    protected $bg;

    /**
     * 验证成功后是否重置
     *
     * @var bool
     */
    protected $reset;
    
    /**
     * 构造配置
     *
     * @var bool
     */
    
    public function __construct(){
        $this->codeSet = C('codeSet');
        $this->expire = C('expire');
        $this->math = C('math');
        
        $this->fontSize = C('fontSize');
        $this->useCurve = C('useCurve');
        $this->imageH = C('imageH');
        
        $this->imageW = C('imageW');
        $this->length = C('length');
        $this->fontttf = __DIR__.'/assets/ttfs/elephant.ttf';
        
        $this->useImgBg = C('useImgBg');
        $this->bg = C('bg');
        $this->reset = C('reset');
    }
    

    
    /**
     * 生成验证码字符串
     *
     * @return string 验证码字符串
     */
    public function generate() {
        // 生成验证码字符串
        $code = $this->createCode();
        // 保存验证码到缓存或数据库
        $this->saveCode($code['value']);

        // 生成验证码图片
        $image = $this->createImage($code['key']);

        // 输出验证码图片
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);

        // 返回验证码字符串
        return $code;
    }

    /**
     * 验证验证码是否正确
     *
     * @param string $code 验证码字符串
     * @return bool 验证结果，正确为 true，错误为 false
     */
    public function check($code) {
        // 从缓存或数据库中获取验证码
        $savedCode = strtolower($this->getSavedCode());
        
        // 验证验证码是否正确
        $result = false;
        if ($code == $savedCode) {
            $result = true;
            if ($this->reset) {
                // 重置验证码
                $this->resetCode();
            }
        }

        return $result;
    }

    /**
     * 生成验证码字符串
     *
     * @return string 验证码字符串
     */
    public function createCode() {
        $code = '';
        if ($this->math) {
            $this->useZh  = false;
            $this->length = 5;
            $x   = random_int(10, 30);
            $y   = random_int(1, 9);
            $code = "{$x}+{$y}= ?";
            $key = $x + $y;
            $key .= '';
        }else{
            $charset = $this->codeSet;
            $codeSetLen = strlen($charset) - 1;
            for ($i = 0; $i < $this->length; $i++) {
                $code .= $charset[mt_rand(0, $codeSetLen)];
            }
        }
        
        return [
            'value' => $this->math ? $key:$code,
            'key'   => $code,
        ];
    }

    /**
     * 生成验证码图片
     *
     * @return resource 图片资源
     */
    public function createImage($code) {
        $imageW = $this->imageW == 0 ? $this->length * $this->fontSize * 1.5 : $this->imageW;
        $imageH = $this->imageH == 0 ? $this->fontSize * 2 : $this->imageH;
        
        $image = imagecreate((int)$imageW, (int)$imageH);
        $bgColor = imagecolorallocate($image, $this->bg[0], $this->bg[1], $this->bg[2]);
    
        // 添加背景图片
        if ($this->useImgBg) {
            $this->addImgBg($image);
        }
    
        // 添加验证码字符
        $codeLen = strlen($code);
        
        // 计算验证码字符串的宽度
        $textWidth = $this->fontSize * $codeLen;
        
        // 计算验证码字符串的起始绘制位置，使其居中
        $x = ($imageW - $textWidth) / 2;
        $y = ($imageH - $this->fontSize) / 2 + $this->fontSize; // 加上字体高度的偏移量
        
        for ($i = 0; $i < $codeLen; $i++) {
            $color = imagecolorallocate($image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
            if ($this->fontttf) {
                imagettftext($image, $this->fontSize, mt_rand(-30, 30), (int)$x + $this->fontSize * $i, (int)$y, $color, $this->fontttf, $code[$i]);
            } else {
                imagechar($image, $this->fontSize, (int)$x + $this->fontSize * $i, (int)$y, $code[$i], $color);
            }
        }
    
        // 添加混淆曲线
        if ($this->useCurve) {
            $this->addCurve($image);
        }
    
        // 添加杂点
        if ($this->useNoise) {
            $this->addNoise($image);
        }
    
        return $image;
    }



    /**
     * 添加背景图片
     *
     * @param resource $image 图片资源
     * @return void
     */
    protected function addImgBg($image) {
        $randimages = __DIR__.'/assets/images/'.rand(1,6).'.jpg';
        $bgImage = imagecreatefromjpeg($randimages); // 替换为您的背景图片路径
        $imageW = imagesx($image);
        $imageH = imagesy($image);
        imagecopyresampled($image, $bgImage, 0, 0, 0, 0, $imageW, $imageH, imagesx($bgImage), imagesy($bgImage));
        imagedestroy($bgImage);
    }

    /**
     * 添加曲线
     *
     * @param resource $image 图像资源
     */
    protected function addCurve($image) {
        $imageW = imagesx($image);
        $imageH = imagesy($image);
        $color = imagecolorallocate($image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
        $amplitude = mt_rand(1, 3); // 曲线振幅
        $period = mt_rand(10, 20); // 曲线周期
        $offset = mt_rand(0, 10); // 曲线偏移量
        $step = 0.1; // 曲线步长
        for ($x = 0; $x < $imageW; $x += $step) {
            $y = round($amplitude * sin($x / $period + $offset));
            imagesetpixel($image, round($x), round($y + $imageH / 2), $color);
        }
    }
    
    /**
     * 添加杂点
     *
     * @param resource $image 图像资源
     */
    protected function addNoise($image) {
        $imageW = imagesx($image);
        $imageH = imagesy($image);
        $noiseNum = mt_rand(100, 200); // 杂点数量
        for ($i = 0; $i < $noiseNum; $i++) {
            $x = mt_rand(0, $imageW);
            $y = mt_rand(0, $imageH);
            $color = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($image, $x, $y, $color);
        }
    }
    
    /**
     * 保存验证码
     *
     * @param string $code 验证码
     */
    protected function saveCode($code) {
        // 初始化验证码
        Session()::set('captcha', $code);
    }
    
    /**
     * 获取保存的验证码
     *
     * @return string 验证码
     */
    protected function getSavedCode() {
        // 从 session 获取验证码
        $code = Session()::get('captcha');
        return !empty($code) ? $code : '';
    }
    
    /**
     * 重置验证码
     */
    protected function resetCode() {
        Session()::delete('captcha');
    }

}
