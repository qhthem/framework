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

use Exception;
use qhphp\logs\Log;
use qhphp\request\Request;
use qhphp\response\Response;

/**
 * Handle类，用于处理异常的渲染和报告
 * @author zhaosong
 */
class Handle
{
    protected $render; // 渲染函数
    protected $ignoreReport; // 是否忽略报告

    /**
     * 设置渲染函数
     * @param \Closure $render 渲染函数
     * @author zhaosong
     */
    public function setRender($render)
    {
        $this->render = $render;
    }

    /**
     * 报告异常
     * @param Exception $exception 异常对象
     * @author zhaosong
     */
    public function report(Exception $exception)
    {
       $data = [
            'file'    => $exception->getFile(), // 获取异常发生的文件路径
            'line'    => $exception->getLine(), // 获取异常发生的行号
            'message' => $this->getMessage($exception), // 获取异常信息
            'code'    => $this->getCode($exception), // 获取异常代码
        ];
        $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]"; // 格式化日志信息
        Log::record($log, 'error');
        // 记录错误日志
        
    }

    /**
     * 渲染异常
     * @param Exception $e 异常对象
     * @return mixed 渲染结果
     * @author zhaosong
     */
    public function render(Exception $e)
    {
        if ($this->render && $this->render instanceof \Closure) { // 如果设置了渲染函数且为闭包
            $result = call_user_func_array($this->render, [$e]); // 调用渲染函数
            if ($result) {
                return $result; // 如果渲染函数有返回值，则返回该值
            }
        }

        if ($e instanceof Exception) { // 如果是Exception
            return $this->convertExceptionToResponse($e); // 渲染Exception
        } else {
            return $this->convertExceptionToResponse($e); // 转换异常为响应
        }
    }

    /**
     * 将异常转换为响应
     * @param Exception $exception 异常对象
     * @return mixed 响应对象
     * @author zhaosong
     */
    protected function convertExceptionToResponse(Exception $exception)
    {
        $message = $this->getMessage($exception); // 获取异常信息
        // 收集异常数据
        if (APP_DEBUG) { // 如果是调试模式
            // 调试模式下的详细错误信息收集
            $traces[] = [
                'name' => $this::class,
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'code' => $this->getCode($exception),
                'message' => $this->getMessage($exception),
                'trace' => $exception->getTrace(),
                'source' => $this->getSourceCode($exception),
            ];
    
            $tables[] = [
                'GET Data' => $_GET,
                'POST Data' => $_POST,
                'Files' => $exception->getFile(),
                'Cookies' => $_COOKIE,
                'Session' => ''.session_name().' = '.session_id().'' ?? [],
            ];
        } else {
            $this->report($exception); // 报告异常
            $file = $exception->getFile();
            $line = $exception->getLine();
        }

        // 清理输出缓冲区
        while (ob_get_level() > 1) {
            ob_end_clean();
        }

        if(Request::isPost()){ // 如果是POST请求
            $Response = new Response();
            $file = empty($file) ? []:$file;
            $line = empty($line) ? 0:$line;
            $debug = ['status'=>0,'msg' => $this->getMessage($exception),'file' => $file,'line' => $line];
            return $Response->code(200)->json($debug);
        }
        else { // 如果不是POST请求
            if(APP_DEBUG){
                 Createdebug()::addmsg($message);
                 Createdebug()::debugMessage();
            }
            
            $exception_tmpl = C('exception_tmpl'); // 获取异常模板路径
            
            if (!is_file($exception_tmpl)){
                showmsg(Lang('The template file for the abnormal page does not exist!'),false,0);
            }
            
            $thinking = Lang('Code creates the future, thinking changes the world.');
            
            include($exception_tmpl); // 包含异常模板
        }
        
        exit; // 退出程序
    }

    /**
     * 获取异常代码
     * @param Exception $exception 异常对象
     * @return int 异常代码
     * @author zhaosong
     */
    protected function getCode(Exception $exception)
    {
        $code = $exception->getCode(); // 获取异常代码
        if (!$code && $exception instanceof ErrorException) { // 如果异常代码不存在且异常是ErrorException类型
            $code = $exception->getSeverity(); // 获取错误的严重程度作为代码
        }
        return $code;
    }

    /**
     * 获取异常信息
     * @param Exception $exception 异常对象
     * @return string 异常信息
     * @author zhaosong
     */
    protected function getMessage(Exception $exception)
    {
        $message = $exception->getMessage(); // 获取异常信息
        return $message; // 返回异常信息
    }

    /**
     * 获取异常源代码
     * @param Exception $exception 异常对象
     * @return array 源代码数组
     * @author zhaosong
     */
    protected function getSourceCode(Exception $exception)
    {
        // 读取前9行和后9行源代码
        $line  = $exception->getLine();
        $first = ($line - 9 > 0) ? $line - 9 : 1;

        try {
            $contents = file($exception->getFile()); // 读取异常发生文件的内容
            $source   = [
                'first'  => $first,
                'source' => array_slice($contents, $first - 1, 19), // 获取异常发生前后的源代码
            ];
        } catch (Exception $e) {
            $source = []; // 如果读取文件失败，则返回空数组
        }
        return $source;
    }


    /**
     * 获取用户定义的常量
     * @return array 常量数组
     * @author zhaosong
     */
    private static function getConst()
    {
        return get_defined_constants(true)['user']; // 获取用户定义的常量
    }
}