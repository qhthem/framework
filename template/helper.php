<?php
// +----------------------------------------------------------------------
// | Template助手函数
// +----------------------------------------------------------------------
use qhphp\template\Template;
use qhphp\template\View;

/**
 * 视图助手函数
 *
 * @param string $app 应用名称
 * @param string $html 模板文件名
 * @param string $theme 主题名称
 * @return string 缓存文件路径
 * @author zhaosong
 */
if (!function_exists('view')) 
{
    function view($app = 'index', $html = 'index')
    {   $view = new View($app,$html);
        $cacheFile = $view->theme()
        ->cacheDirectory()->templateFile()->cacheFile()->template();
        return $cacheFile;
    }
}

/**
 * 为字符串添加反斜杠
 *
 * @param mixed $string 需要添加反斜杠的字符串或数组
 * @return mixed 添加反斜杠后的字符串或数组
 * @author zhaosong
 */
if (!function_exists('new_addslashes')) {
    function new_addslashes($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $value) {
                $string[$key] = new_addslashes($value);
            }
        } else {
            $string = addslashes($string);
        }

        return $string;
    }
}

/**
 * 将字符串条件转换为数组形式的WHERE条件
 *
 * @param string $conditionsString 字符串形式的条件
 * @return array 数组形式的WHERE条件
 * @author zhaosong
 */
if (!function_exists('convertStringToWhereArray')) {
    function convertStringToWhereArray($conditionsString)
    {
        $conditionsArray = explode(',', $conditionsString);
        $where = array();
        foreach ($conditionsArray as $condition) {
            list($key, $value) = explode('=', $condition);
            $key = trim($key);
            $value = trim($value);
            $where[$key] = $value;
        }
        return $where;
    }
}

/**
 * 将数组转换为 HTML 格式的字符串
 *
 * @param array $array 要转换的数组
 * @return string 转换后的 HTML 字符串
 */
if (!function_exists('var_export_to_html')) {
    function var_export_to_html($array) {
        if (is_array($array)) {
			$str = 'array(';
			foreach ($array as $key=>$val) {
				if (is_array($val)) {
					$str .= "'$key'=>".var_export_to_html($val).",";
				} else {
					if (in_array($key, array('where', 'sql'))) {
						$str .= "'$key'=>\"".$val."\",";
					}else{
						if (strpos($val, '$')===0) {
							$str .= "'$key'=>$val,";
						} else {
							$str .= "'$key'=>'".new_addslashes($val)."',";
						}						
					}
				}
			}
			return $str.')';
		}
		return false;    
    }
}

