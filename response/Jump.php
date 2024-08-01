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
namespace qhphp\response;
use Exception;
use qhphp\response\Response;
class Jump
{
    /**
     * 操作成功跳转的快捷方法
     * @access public
     * @param mixed  $msg    提示信息
     * @param string $url    跳转的 URL 地址
     * @param mixed  $data   返回的数据
     * @param int    $wait   跳转等待时间
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws Exception
     */
    public function success($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        if (is_null($url) && !is_null(Request()::server('HTTP_REFERER'))) {
            $url = Request()::server('HTTP_REFERER');
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = 'javascript:history.back(-1);';
        }

        $type = $this->getResponseType();
        $result = [
            'code' => 200,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];
        
        $status = $result['code'];
        if ('html' == strtolower($type)) {
            $templatePath = C('dispatch_tmpl');
            // 检查文件是否存在
            if (file_exists($templatePath)) {
                include $templatePath;
            } else {
                // 处理错误，例如记录日志、返回错误消息或重定向到错误页面
                throw new Exception(Lang('The template file for the abnormal page does not exist!'),500);
            }
        }
        else{
           json(['status'=>$status,'msg'=>$msg,'data'=>$data]);
        }
        exit;
    }

    /**
     * 操作错误跳转的快捷方法
     * @access public
     * @param mixed  $msg    提示信息
     * @param string $url    跳转的 URL 地址
     * @param mixed  $data   返回的数据
     * @param int    $wait   跳转等待时间
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws Exception
     */
    public function error($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        if (is_null($url)) {
            $url = Request()::isPost() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = 'javascript:history.back(-1);';
        }

        $type = $this->getResponseType();
        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];
        $status = $result['code'];
        if ('html' == strtolower($type)) {
            $templatePath = C('dispatch_tmpl');
            // 检查文件是否存在
            if (file_exists($templatePath)) {
                include $templatePath;
            } else {
                // 处理错误，例如记录日志、返回错误消息或重定向到错误页面
                throw new Exception(Lang('The template file for the abnormal page does not exist!'),500);
            }
        }
        else{
           
            json(['status'=>$status,'msg'=>$msg,'data'=>$data]);
        }
        exit;
    }


    /**
     * URL 重定向
     * @access protected
     * @param string    $url    跳转的 URL 表达式
     * @param array|int $params 其它 URL 参数
     * @param int       $code   http code
     * @param array     $with   隐式传参
     * @return void
     * @throws Exception
     */
    public function redirect($url,$code = 302)
    {
        (new Response())->code($code)->redirect($url);
        
    }

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        return Request()::isPost()
            ? 'json'
            : 'html';
    }
}
