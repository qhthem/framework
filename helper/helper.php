<?php
// +----------------------------------------------------------------------
// | 助手类
// +----------------------------------------------------------------------
use qhphp\helper\Time;

if (!function_exists('Times')) {
    /**
     * 创建一个Time类的实例
     *
     * @return Time Time类的实例
     * @author zhaosong
     */
    function Times()
    {
        $Time = Time::class;
        return $Time;
    }
}