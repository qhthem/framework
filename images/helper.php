<?php
// +----------------------------------------------------------------------
// | 图片处理助手函数
// +----------------------------------------------------------------------
use qhphp\images\Images;

/**
 * 获取 Images 实例。
 *
 * @return Images 返回 Images 实例
 */
if (!function_exists('Images')) {
    function Images($path)
    {
        return new Images($path);
    }
}