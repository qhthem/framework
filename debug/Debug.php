<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] 错误输出处理类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\debug;
use Exception;

use qhphp\exception\ErrorException;
use qhphp\exception\Handle;
use qhphp\exception\ThrowableError;

/**
 * 调试类，用于注册错误处理、异常处理以及程序关闭时的处理
 * @author zhaosong
 */
class Debug {

    /**
     * 注册错误处理、异常处理和程序关闭时的处理函数
     * @author zhaosong
     */
    public static function register()
    {
        error_reporting(E_ALL); // 设置错误报告级别为所有错误
        set_error_handler([__CLASS__, 'Error']); // 设置错误处理函数
        set_exception_handler([__CLASS__, 'Exception']); // 设置异常处理函数
        register_shutdown_function([__CLASS__, 'Shutdown']); // 注册程序关闭时执行的函数
    }

    /**
     * 错误处理函数
     * @param int $errno 错误编号
     * @param string $errstr 错误信息
     * @param string $errfile 发生错误的文件路径
     * @param int $errline 发生错误的行号
     * @author zhaosine
     */
    public static function Error($errno, $errstr, $errfile, $errline)
    {
        $exception = new ErrorException($errno, $errstr, $errfile, $errline); // 创建错误异常对象

        if (error_reporting() & $errno) { // 如果当前设置的错误报告级别包含此错误
            throw $exception; // 抛出异常
        }

        (new Handle())->render($exception)->send(); // 渲染并发送错误信息
    }

    /**
     * 异常处理函数
     * @param \Exception $e 异常对象
     * @author zhaosong
     */
    public static function Exception($e)
    {
        if (!$e instanceof \Exception) { // 如果传入的不是Exception对象
            $e = new ThrowableError($e); // 转换为ThrowableError对象
        }

        (new Handle())->render($e)->send(); // 渲染并发送异常信息
    }

    /**
     * 程序关闭时的处理函数
     * @author zhaosong
     */
    public static function Shutdown()
    {
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) { // 获取最后发生的错误，并判断是否为致命错误
            self::Exception(new ErrorException(
                $error['type'], $error['message'], $error['file'], $error['line'] // 创建错误异常对象
            ));
        }
    }

    /**
     * 判断错误类型是否为致命错误
     * @param int $type 错误类型
     * @return bool 是否为致命错误
     * @author zhaosong
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]); // 判断错误类型是否在致命错误数组中
    }

}