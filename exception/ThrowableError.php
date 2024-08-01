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
namespace qhphp\exception;
use Throwable;

/**
 * ThrowableError类，用于将Throwable类型的错误转换为ErrorException
 * @author zhaosong
 */
class ThrowableError extends \ErrorException
{
    /**
     * 构造函数，接收一个Throwable类型的错误对象
     * @param \Throwable $e Throwable类型的错误对象
     * @author zhaosong
     */
    public function __construct(\Throwable $e)
    {
        // 根据错误类型设置错误信息和严重程度
        if ($e instanceof \ParseError) {
            $message  = 'Parse error: ' . $e->getMessage();
            $severity = E_PARSE;
        } elseif ($e instanceof \TypeError) {
            $message  = 'Type error: ' . $e->getMessage();
            $severity = E_RECOVERABLE_ERROR;
        } else {
            $message  = 'Fatal error: ' . $e->getMessage();
            $severity = E_ERROR;
        }

        // 调用父类构造函数初始化错误信息
        parent::__construct(
            $message,
            $e->getCode(),
            $severity,
            $e->getFile(),
            $e->getLine()
        );

        // 设置异常的堆栈跟踪
        $this->setTrace($e->getTrace());
    }

    /**
     * 设置异常的堆栈跟踪
     * @param array $trace 堆栈跟踪数组
     * @author zhaosong
     */
    protected function setTrace($trace)
    {
        $traceReflector = new \ReflectionProperty('Exception', 'trace'); // 获取Exception类的trace属性反射对象
        $traceReflector->setAccessible(true); // 设置trace属性为可访问
        $traceReflector->setValue($this, $trace); // 设置当前对象的trace属性值
    }
}