<?php
// +----------------------------------------------------------------------
// | 调试程序操作类助手函数
// +----------------------------------------------------------------------
use qhphp\debug\Html;

/**
 * Createdebug函数，用于创建并返回Html调试类的实例
 * @return string Html类的完全限定名称
 * @author zhaosong
 */
if (!function_exists('Createdebug')) {
    function Createdebug()
    {
        return Html::class; // 返回Html类的完全限定名称
    }
}