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
// +----------------------------------------------------------------------
// $image = new Image('path/to/image.jpg');
// +----------------------------------------------------------------------
// $image->crop(100, 100, 200, 200);
// +----------------------------------------------------------------------
// $image->flip('horizontal');
// +----------------------------------------------------------------------
// $image->watermark('path/to/watermark.png', 50, 50);
// +----------------------------------------------------------------------
// $image->text('Hello World', 100, 100, [255, 255, 255], 12, 'path/to/font.ttf');
// +----------------------------------------------------------------------
// $image->save('path/to/new/image.jpg', true); // 覆盖现有文件
namespace qhphp\images;
use Exception;
class Images
{
    private $image;
    private $width;
    private $height;
    private $type;

    /**
     * 构造函数，用于打开一个图片文件并设置图片资源。
     *
     * @param string $imagePath 图片文件路径
     *
     * @throws Exception 如果图片类型不支持，则抛出异常
     */
    public function __construct($imagePath)
    {
        $info = getimagesize($imagePath);
        $this->width = $info[0];
        $this->height = $info[1];
        $this->type = $info[2];
        switch ($this->type) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($imagePath);
                break;
            default:
                throw new Exception('不支持的图片类型');
        }
    }

    /**
     * 裁剪图片。
     *
     * @param int $x X坐标
     * @param int $y Y坐标
     * @param int $width 宽度
     * @param int $height 高度
     *
     * @return $this
     */
    public function crop($x, $y, $width, $height)
    {
        $newImage = imagecreatetruecolor($width, $height);
        imagecopy($newImage, $this->image, 0, 0, $x, $y, $width, $height);
        $this->image = $newImage;
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * 保存图片。
     *
     * @param string $path 保存路径
     *
     * @throws Exception 如果图片类型不支持，则抛出异常
     */
    public function save($path)
    {
        switch ($this->type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $path);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->image, $path);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->image, $path);
                break;
            default:
                throw new Exception('不支持的图片类型');
        }
        imagedestroy($this->image);
    }

    /**
     * 翻转图片。
     *
     * @param int $mode 翻转模式，默认为 null
     *
     * @return $this
     */
    public function flip($mode = null)
    {
        $newImage = imagecreatetruecolor($this->width, $this->height);
        if ($mode === null) {
            for ($x = 0; $x < $this->width; $x++) {
                imagecopy($newImage, $this->image, $x, 0, $this->width - $x - 1, 0, 1, $this->height);
            }
        } else {
            for ($y = 0; $y < $this->height; $y++) {
                imagecopy($newImage, $this->image, 0, $y, 0, $this->height - $y - 1, $this->width, 1);
            }
        }
        $this->image = $newImage;
        return $this;
    }

    /**
     * 旋转图片。
     *
     * @param int $degrees 旋转角度，默认为 90
     *
     * @return $this
     */
    public function rotate($degrees = 90)
    {
        $newImage = imagecreatetruecolor($this->height, $this->width);
        $backgroundColor = imagecolorallocate($newImage, 255, 255, 255);
        imagefilledrectangle($newImage, 0, 0, $this->height, $this->width, $backgroundColor);
        imagecopyresampled(
            $newImage,
            $this->image,
            0,
            0,
            0,
            0,
            $this->height,
            $this->width,
            $this->width,
            $this->height
        );
        $this->image = $newImage;
        $temp = $this->width;
        $this->width = $this->height;
        $this->height = $temp;
        return $this;
    }
    
    /**
     * 添加水印
     *
     * @param string $waterPath 水印图片路径
     * @param int $position 水印位置，默认为右下角
     * @param int $alpha 水印透明度，默认为100
     * @return $this
     * @throws Exception
     */
    public function water($waterPath, $position, $alpha = 100)
    {
        $waterInfo = getimagesize($waterPath);
        $waterWidth = $waterInfo[0];
        $waterHeight = $waterInfo[1];
        $waterType = $waterInfo[2];
        switch ($waterType) {
            case IMAGETYPE_JPEG:
                $waterImage = imagecreatefromjpeg($waterPath);
                break;
            case IMAGETYPE_PNG:
                $waterImage = imagecreatefrompng($waterPath);
                break;
            case IMAGETYPE_GIF:
                $waterImage = imagecreatefromgif($waterPath);
                break;
            default:
                throw new Exception('Unsupported water image type');
        }
        $x = 0;
        $y = 0;
        switch ($position) {
            case 1:
                $x = 0;
                $y = 0;
                break;
            case 2:
                $x = ($this->width - $waterWidth) / 2;
                $y = 0;
                break;
            case 3:
                $x = $this->width - $waterWidth;
                $y = 0;
                break;
            case 4:
                $x = 0;
                $y = ($this->height - $waterHeight) / 2;
                break;
            case 5:
                $x = ($this->width - $waterWidth) / 2;
                $y = ($this->height - $waterHeight) / 2;
                break;
            case 6:
                $x = $this->width - $waterWidth;
                $y = ($this->height - $waterHeight) / 2;
                break;
            case 7:
                $x = 0;
                $y = $this->height - $waterHeight;
                break;
            case 8:
                $x = ($this->width - $waterWidth) / 2;
                $y = $this->height - $waterHeight;
                break;
            case 9:
                $x = $this->width - $waterWidth;
                $y = $this->height - $waterHeight;
                break;
            default:
                throw new Exception('Invalid water position',404);
        }
        imagecopy($this->image, $waterImage, $x, $y, 0, 0, $waterWidth, $waterHeight);
        return $this;
    }

    /**
     * 添加文本
     *
     * @param string $text 文本内容
     * @param string $font 字体路径
     * @param int $size 字体大小
     * @param string $color 字体颜色，格式为十六进制，例如：#FFFFFF
     * @param array $position 文本位置，默认为左上角
     * @return $this
     */
    public function text($text, $font, $size, $color, $position = [100, 100])
    {
        $color = imagecolorallocate($this->image, hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2)));
        $box = imagettfbbox($size, 0, $font, $text);
        $textWidth = $box[4] - $box[0];
        $textHeight = $box[5] - $box[1];
        $x = $position[0] - $textWidth / 2;
        $y = $position[1] + $textHeight / 2;
        imagettftext($this->image, $size, 0, $x, $y, $color, $font, $text);
        return $this;
    }
}