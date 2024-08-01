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
declare(strict_types=1);

namespace qhphp\router;
use Exception;

/**
 * 路由类
 *
 * @author zhaosong
 */
class Router
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->pathinfo_url();
    }

    /**
     * 获取格式化后的应用名称
     *
     * @return string
     */
    public function formatAppnName(): string
    {
        $m = GetRoute('app');
        $m = $this->safe_deal($m);
        return !empty($m) ? $m : C('default_app');
    }

    /**
     * 获取格式化后的控制器名称
     *
     * @return string
     */
    public function formatClassName(): string
    {
        $c = GetRoute('controller');
        $c = $this->safe_deal($c);
        return !empty($c) ? $c : C('default_controller');
    }

    /**
     * 获取格式化后的方法名称
     *
     * @return string
     */
    public function formatActionName(): string
    {
        $a = GetRoute('action');
        $a = $this->safe_deal($a);
        return !empty($a) ? $a : C('default_action');
    }

    /**
     * 安全处理字符串
     *
     * @param string $str
     * @return string
     */
    private function safe_deal($str): string
    {
        if (!is_string($str)) return '';
        $str = trim($str);
        if (strlen($str) > 128) throw new Exception('parameter length cannot exceed 128 character.', 404);
        return str_replace(array('/', '.'), '', $str);
    }

    /**
     * 解析PATH_INFO
     *
     * @return bool
     */
    private function pathinfo_url(): bool
    {
        if (!isset($_GET['s']) || !is_string($_GET['s'])) {
            return false;
        }
    
        $_SERVER['PATH_INFO'] = $_GET['s'];
        unset($_GET['s']);
    
        if (!isset($_SERVER['PATH_INFO']) || empty($_SERVER['PATH_INFO'])) {
            return false;
        }
    
        $_SERVER['PATH_INFO'] = str_ireplace(['.html', 'index.php'], '', $_SERVER['PATH_INFO']);
        if(C('route_mapping')) $this->mapping(C('route_rules'));
    
        $pathinfo = array_filter(explode('/', trim($_SERVER['PATH_INFO'], '/')));
        
        $_GET = array_merge([
            'app' => '',
            'controller' => '',
            'action' => '',
        ], $_GET);
        
        $path_segments = array_slice($pathinfo, 0, 3);
        $_GET['app'] = array_shift($path_segments);
        $_GET['controller'] = array_shift($path_segments);
        $_GET['action'] = array_shift($path_segments);
    
        $total = count($pathinfo);
        for ($i = 3; $i < $total; $i += 2) {
            if (isset($pathinfo[$i + 1])) $_GET[$pathinfo[$i]] = $pathinfo[$i + 1];
        }
    
        return true;
    }

    /**
     * 路由映射
     *
     * @param array $rules
     * @return void
     */
    private function mapping($rules): void
    {
        $original_rules = Cache()->get('Route');
        $new_rules = [];
        
        foreach ($original_rules as $key => $rule) {
            if(is_array($rule)){
                foreach ($rule as $key => $value) {
                    $new_rules[$key] = $value;
                }
            }else{
                $new_rules[$key] = $rule;
            }
        }
        
        $rules = !empty($original_rules) ? array_merge(C('route_build'), $new_rules) : C('route_build');
    
        if (!is_array($rules)) return;
        $pathinfo = trim($_SERVER['PATH_INFO'] ?? '', '/');
        if (!$pathinfo) return;
    
        $matched = false;
        foreach ($rules as $k => $v) {
            $reg = '/' . $k . '/i';
            if (preg_match($reg, $pathinfo)) {
                $res = preg_replace($reg, $v, $pathinfo);
                $_SERVER['PATH_INFO'] = '/' . $res;
                $matched = true;
                break;
            }
        }
    
        // 如果没有匹配到任何规则，重置 PATH_INFO
        if (!$matched) {
            $_SERVER['PATH_INFO'] = $pathinfo;
        }
    }
}