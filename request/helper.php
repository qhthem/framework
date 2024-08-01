<?php
// +----------------------------------------------------------------------
// | 请求类助手函数
// +----------------------------------------------------------------------
use qhphp\request\Request;

if (!function_exists('Request')) {
    /**
     * 获取 Request 实例
     *
     * @return \CodeIgniter\HTTP\Request
     */
    function Request()
    {
        return Request::class;
    }
}

/**
 * 判断请求类型是否为GET。
 *
 * @return bool 如果请求类型为GET，则返回true，否则返回false
 */
if (!function_exists('isGet')) {
    function isGet()
    {
        return Request::isGet();
    }
}

/**
 * 判断请求类型是否为POST。
 *
 * @return bool 如果请求类型为POST，则返回true，否则返回false
 */
if (!function_exists('isPost')) {
    function isPost()
    {
        return Request::isPost();
    }
}

/**
 * 判断请求是否为AJAX请求。
 *
 * @return bool 如果请求为AJAX请求，则返回true，否则返回false
 */
if (!function_exists('isAjax')) {
    function isAjax()
    {
        return Request::isAjax();
    }
}

/**
 * 获取客户端IP地址。
 *
 * @return string 返回客户端IP地址
 */
if (!function_exists('getip')) {
    function getip()
    {
        return Request::ip();
    }
}

/**
 * 获取请求参数。
 *
 * @param string $key 参数名称
 * @param mixed $default 如果参数不存在，则返回此默认值
 * @return mixed 返回参数值或默认值
 */
if (!function_exists('input')) {
    function input($key = null, $default = null)
    {
        return Request::input($key, $default);
    }
}

/**
 * 获取GET参数。
 *
 * @param string $key 参数名称
 * @param mixed $default 如果参数不存在，则返回此默认值
 * @return mixed 返回参数值或默认值
 */
if (!function_exists('get_param')) {
    function get_param($key, $default = null)
    {
        return Request::param($key, $default);
    }
}

/**
 * 获取当前域名
 *
 * @param string $protocol 协议（http 或 https）
 * @return string 当前域名
 * @author zhaosong
 */
if (!function_exists('SITE_URL')){
    function SITE_URL()
    {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'];
        return $protocol . $domain.'/';
    }    
}

if (!function_exists('token')) {
    /**
     * 获取Token令牌
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function token(string $name = '__token__', string $type = 'md5'): string
    {
        return Request::createToken($name, $type);
    }
}

if (!function_exists('token_field')) {
    /**
     * 生成令牌隐藏表单
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function token_field(string $name = '__token__', string $type = 'md5'): string
    {
        $token = Request::createToken($name, $type);

        return '<input type="hidden" name="' . $name . '" value="' . $token . '" />';
    }
}

if (!function_exists('token_meta')) {
    /**
     * 生成令牌meta
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function token_meta(string $name = '__token__', string $type = 'md5'): string
    {
        $token = Request::createToken($name, $type);

        return '<meta name="csrf-token" content="' . $token . '">';
    }
}

/**
 * 验证POST提交的数据是否符合指定的键值要求。
 * 
 * @param string $keysStr 需要验证的键名列表，以逗号分隔。
 * @param array $params POST提交的数据数组。
 * @return array|json 如果所有键值都存在，则返回一个包含对应键值为true的数组；如果有键值为空，则返回JSON格式的错误信息。
 * @author zhaosong
 */
if (!function_exists('validatePostValues')){
    function validatePostValues(string $keysStr,array $params) {
        // 将键名列表字符串按逗号分隔为数组
        $keys = explode(',', $keysStr);
        // 初始化结果数组
        $results = [];
        // 遍历键名数组
        foreach ($keys as $key) {
            // 如果对应的POST参数为空
            if (empty($params[$key])) {
                // 返回错误信息的JSON对象
                $msg = "提交的 ".$key." 值为空，请填写。";
                return json(['status'=>0,'msg'=>$msg]);
            } else {
                // 如果POST参数不为空，则在结果数组中对应键值设为true
                $results[$key] = true;
            }
        }
        // 所有键值都存在时，返回结果数组
        return $results;
    }
}