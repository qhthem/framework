<?php
// +----------------------------------------------------------------------
// | 响应输出类助手函数
// +----------------------------------------------------------------------
use qhphp\response\Response;
use qhphp\request\Request;
use qhphp\response\Jump;
/**
 * 返回JSON响应。
 *
 * @param mixed $data 要返回的数据
 * @param int $code HTTP状态码，默认为200
 * @param array $headers 要添加到响应中的HTTP头，默认为空数组
 * @return void
 */
if (!function_exists('json')) {
    function json($data, $code = 200)
    {
        $Response = new Response();
        return $Response->code($code)->contentType()->json($data);
    }
}

if (!function_exists('download')) {
    /**
     * 下载文件
     *
     * @param string $filepath 文件路径
     * @param string $filename 文件名（可选）
     * @author zhaosong
     */
    function download($filepath, $filename = '') {
        if (!$filename) {
            $filename = basename($filepath);
        }
        if (is_ie()) {
            $filename = rawurlencode($filename);
        }
        $filetype = strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
        $filesize = sprintf("%u", filesize($filepath));
        if (ob_get_length() !== false) {
            @ob_end_clean();
        }
        header('Pragma: public');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Encoding: none');
        header('Content-type: ' . $filetype);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-length: ' . $filesize);
        readfile($filepath);
        exit;
    }
}

if (!function_exists('is_ie')) {
    /**
     * 判断是否为 IE 浏览器
     *
     * @return bool 如果是 IE 浏览器则返回 true，否则返回 false
     * @author zhaosong
     */
    function is_ie() {
        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if ((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) {
            return false;
        }
        if (strpos($useragent, 'msie ') !== false) {
            return true;
        }
        return false;
    }
}

/**
 * 显示消息函数，支持POST和GET请求，根据参数类型返回不同的响应格式。
 * 
 * @author zhaosong
 * 
 * @param string $msg 消息内容，默认为空字符串
 * @param mixed ...$params 可变参数列表，支持以下类型：
 *                         - 字符串：作为跳转URL
 *                         - 数组：作为响应数据
 *                         - 布尔值：决定响应类型，true为成功，false为错误
 *                         - 整数：作为等待时间（秒）
 */
if (!function_exists('showmsg')) {
    function showmsg($msg = '', ...$params)
    {
        $Response = new Jump(); // 创建响应对象
        $url = null; // 初始化URL变量
        $data = null; // 初始化数据变量
        $type = true; // 初始化响应类型为成功
        $wait = 3; // 初始化等待时间为3秒

        // 解析可变参数
        foreach ($params as $param) {
            if (is_string($param)) {
                $url = $param; // 设置URL
            } elseif (is_array($param)) {
                $data = $param; // 设置数据
            } elseif (is_bool($param)) {
                $type = $param; // 设置响应类型
            } elseif (is_int($param)) {
                $wait = $param; // 设置等待时间
            }
        }

        if ($type) {
            $Response->success($msg, $url, $data, $wait);
        } else {
            $Response->error($msg, $url, $data, $wait);
        }
    }
}