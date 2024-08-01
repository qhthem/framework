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
namespace qhphp\request;

class Request
{
    
    /**
     * 获取请求方法（GET, POST, PUT, DELETE 等）
     *
     * @return string 请求方法
     */
    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 判断请求方法是否为 GET
     *
     * @return boolean 是否为 GET 请求
     */
    public static function isGet() {
        return self::method() === 'GET';
    }

    /**
     * 判断请求方法是否为 POST
     *
     * @return boolean 是否为 POST 请求
     */
    public static function isPost() {
        return self::method() === 'POST';
    }

    /**
     * 判断请求是否为 AJAX 请求
     *
     * @return boolean 是否为 AJAX 请求
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' ? true : false;
    }

    /**
     * 获取客户端 IP 地址
     *
     * @return string IP 地址
     */
    public static function ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 获取 GET 参数
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值或默认值
     */
    public static function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * 获取 POST 参数
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值或默认值
     */
    public static function post($key = null, $default = null) {
        $postData = file_get_contents('php://input');
        $_POST = json_decode($postData, true);
        
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * 获取 GET 或 POST 参数
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值或默认值
     */
    public static function input($key = null, $default = null) {
        $postData = file_get_contents('php://input');
        $_POST = json_decode($postData, true) ?? array();
        $params = array_merge($_GET, $_POST);
        if ($key === null) {
            return $params;
        }
        return isset($params[$key]) ? $params[$key] : $default;
    }
    
    /**
     * 获取指定的 GET 或 POST 参数
     *
     * @param array $variables 参数名数组
     * @return array 参数值数组
     */
    public static function only($variables) {
        $requestData = [];
        foreach ($variables as $variable) {
            if (isset($_REQUEST[$variable])) {
                $requestData[$variable] = $_REQUEST[$variable];
            }
        }
        return $requestData;
    }   

    /**
     * 获取当前 URL。
     *
     * @return string 当前 URL
     */
    public static function getCurrentUrl()
    {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $url = $protocol . '://' . $host . $uri;
        return $url;
    }
    
    
    /**
     * 获取指定参数的值。
     *
     * @param string $key 参数名称
     * @param mixed $default 默认值，如果参数不存在或为空，则返回此默认值
     * @return mixed 参数值
     */
    public static function param(string $key, $default = null)
    {
        $params = explode('.', $key);
        $params[0] == 'get' ? $_GET['s'] : $_POST['s'];
        preg_match("/\/{$params[1]}\/(\d+)/", $_GET['s'], $matches);
        return empty($matches[1]) ? $default : $matches[1];
    }
    
    
    /**
     * 获取除指定键之外的所有请求数据。
     *
     * @param array $keys 要排除的键的数组。
     * @return array 不包含指定键的请求数据的数组。
     */
    public static function except($keys = [])
    {
        $data = $_REQUEST;
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                unset($data[$key]);
            }
        }
        
        return $data;
   }
   
     /**
     * 获取 HTTP 请求头的值
     *
     * @param string $name 请求头的名称
     * @return string|null 请求头的值，如果请求头不存在则返回 null
     */
    public static function header(string $name) {
        $name = strtoupper(str_replace('-', '_', $name));
        $header = isset($_SERVER['HTTP_' . $name]) ? $_SERVER['HTTP_' . $name] : null;
        return $header;
    }
    
    /**
     * 获取上传的文件信息。
     *
     * @param string $key 文件输入字段名称
     * @return array|null 返回文件信息数组，如果文件不存在则返回 null
     */
    public static function file(string $key)
    {
        if (isset($_FILES[$key])) {
            return $_FILES[$key];
        }
        return null;
    }
    
    /**
     * 获取服务器变量值
     * @param string $name 服务器变量名，默认为空
     * @param string $default 如果指定的服务器变量不存在，则返回此默认值，默认为空
     * @return mixed 返回服务器变量的值，如果指定的服务器变量不存在且没有设置默认值，则返回null
     * @author zhaosong
     */
    public static function server(string $name, string $default = '')
    {
        if (isset($_SERVER[$name])) {
            $referer = $_SERVER[$name];
            return $referer;
        } else {
            return $default;
        }
    }
    
    /**
     * 创建令牌
     * @param string $name 令牌名称，默认为'__token__'
     * @param string|callable $type 令牌类型，默认为'md5'，如果传入的是可调用对象，则使用该对象生成令牌
     * @return string 返回生成的令牌
     * @author zhaosong
     */
    public static function createToken(string $name = '__token__', $type = 'md5'): string
    {
        $type  = is_callable($type) ? $type : 'md5';
        $token = call_user_func($type, self::server('REQUEST_TIME_FLOAT'));
    
        Session()::set($name, $token);
    
        return $token;
    }
    
    /**
     * 检查令牌是否有效
     * @param string $token 令牌名称，默认为'__token__'
     * @param array $data 验证数据，默认为空，如果为空则使用POST数据
     * @return bool 如果令牌有效则返回true，否则返回false
     * @author zhaosong
     */
    public static function checkToken(string $token = '__token__', array $data = []): bool
    {
        if (!Session()::has($token)) {
            // 令牌数据无效
            return false;
        }
    
        // Header验证
        if (self::header('X-CSRF-TOKEN') && Session()::get($token) === self::header('X-CSRF-TOKEN')) {
            // 防止重复提交
            Session()::delete($token); // 验证完成销毁session
            return true;
        }
    
        if (empty($data)) {
            $data = self::post();
        }
    
        // 令牌验证
        if (isset($data[$token]) && Session()::get($token) === $data[$token]) {
            // 防止重复提交
            Session()::delete($token); // 验证完成销毁session
            return true;
        }
    
        // 开启TOKEN重置
        Session()::delete($token);
        return false;
    }
    

}