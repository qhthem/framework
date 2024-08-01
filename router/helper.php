<?php
// +----------------------------------------------------------------------
// | 路由类助手函数
// +----------------------------------------------------------------------
use qhphp\router\Router;

if (!function_exists('get_url')){
    /**
     * 获取当前页面完整URL地址
     */
    function get_url() {
    	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $requestUrl = $_SERVER['REQUEST_URI'];
        return $fullUrl = $protocol . $host . $requestUrl;
    }
    
}

/**
 * 获取当前站点的域名
 *
 * @return string 当前站点的域名（包括协议和主机名）
 */
if (!function_exists('get_Domain')){
     function get_Domain() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $domain = $protocol . $host.'/';
        return $domain;
    }   
}

if (!function_exists('url')) {
    /**
     * 生成 URL 链接。
     *
     * @param string $url       URL 路径
     * @param mixed  $vars      额外的 URL 参数
     * @param string $domain    域名
     * @param bool   $suffix    是否添加后缀
     * @return string 生成的 URL 链接
     */
    function url($url = '', $vars = '', $domain = '/', $suffix = true) {
        $url = trim($url, '/');
        $arr = explode('/', $url);
        $num = count($arr);

        $string = '';
        if ($num == 3) {
            $string .= $url;
        } elseif ($num == 2) {
            $string .= Router()->formatAppnName() . '/' . $url;
        } else {
            $string .= Router()->formatAppnName() . '/' . Router()->formatClassName() . '/' . $url;
        }

        if ($vars) {
            if (!is_array($vars)) {
                parse_str($vars, $vars);
            }
            foreach ($vars as $var => $val) {
                if (!is_array($val) && trim($val) !== '') {
                    $string .= '/' . urlencode($var) . '/' . urlencode($val);
                }
            }
        }

        $string .= $suffix === true ? '.html' : $suffix;

        return $domain . $string;
    }
}



/**
 * 如果不存在 admin_map 函数，则定义一个 admin_map 函数
 *
 * @param string $old_admin 旧的 admin 值
 * @return string 返回新的 admin 值
 */
if (!function_exists('admin_map')) {
    function admin_map($old_admin)
    {
        $arr = C('app_map');
        if(!empty($arr)){
            foreach ($arr as $key => $value){
                $admin = $key;
            }
        }
        
        return empty($admin) ? $old_admin : $admin;
    }
}

if (!function_exists('Router')) {
    /**
     * 获取 Router 实例
     *
     * @return \CodeIgniter\HTTP\Request
     */
    function Router()
    {
        return new Router();
    }
}


if (!function_exists('GetRoute')) {
    
    function GetRoute($key)
    {
        $Route = $_GET[$key] ?? $_POST[$key] ?? '';
        return $Route;
    }
}


if (!function_exists('AppRouter')) {
    /**
     * 获取 Router 实例
     *
     * @return \CodeIgniter\HTTP\Request
     */
    function AppRouter()
    {
        
        
    }
}

