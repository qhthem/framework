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
namespace qhphp\app;

class Model{
    
    /**
     * 生成列表树
     *
     * @param array $data 列表数据
     * @param int $pid 父级 ID
     * @param array $config 配置数组，包含两个元素：第一个元素表示 ID 字段名，第二个元素表示父级 ID 字段名
     * @return array 生成的树形结构
     */
     protected static function _generateListTree(array $data, int $pid = 0, array $config = [])
     {
        $tree = [];
        if ($data && is_array($data)) {
            foreach ($data as $v) {
                if ($v[$config[1]] == $pid) {
                    $children = self::_generateListTree($data, $v[$config[0]], $config);
                    if (!empty($children)) {
                        $v = array_merge($v, ['children' => $children]);
                    }
                    if(empty($v['icon'])){
                        unset($v['icon']);
                    }
                    $tree[] = $v;
                }
            }
        }
        return $tree;
    }
   
    /**
     * 生成树形结构的分类数组。
     *
     * @param array  $cate      分类数组，包含所有分类信息的数组
     * @param string $name      分类名称的键名
     * @param string $lefthtml  分类前缀，用于在分类名称前添加缩进
     * @param int    $pid       父分类 ID，用于查询子分类
     * @param int    $lvl       分类层级，用于设置缩进级别
     * @return array            生成的树形结构数组
     */
    protected static function tree_cate(array $cate, string $name, $id,string $lefthtml = '|— ', int $pid = 0, int $lvl = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['parentid'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['lefthtml'] = str_repeat($lefthtml, $lvl);
                $v['l' . $name] = $v['lefthtml'] . $v[$name];
                $arr[] = $v;
                $arr = array_merge($arr, self::tree_cate($cate, $name,$id, $lefthtml, $v[$id], $lvl + 1));
            }
        }
        return $arr;
    }
    
    
    /**
     * 删除目标目录及其所有文件和子目录
     *
     * @param string $path 要删除的目录路径
     * @param bool $delDir 是否删除目录本身（true 删除，false 不删除）
     * @return bool 删除成功返回 true，失败返回 false
     */
    protected static function del_target_dir(string $path, bool $delDir)
    {
        // 没找到，不处理
        if (!file_exists($path)) {
            return false;
        }
    
        // 打开目录句柄
        $handle = opendir($path);
        if ($handle) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$path/$item")) {
                        self::del_target_dir("$path/$item", $delDir);
                    } else {
                        unlink("$path/$item");
                    }
                }
            }
            closedir($handle);
            if ($delDir) {
                return rmdir($path);
            }
        } else {
            if (file_exists($path)) {
                return unlink($path);
            }
            return false;
        }
    } 
    
    /**
     * 将字节数转换为带单位的字符串。
     *
     * @param int $bytes 字节数
     * @param int $precision 小数点后保留的位数，默认为 2 位
     * @return string 转换后的字符串，例如：1.50 MB
     */
    protected static function formatBytes($bytes, int $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
    
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * 根据文件扩展名判断文件类型。
     *
     * @param string $extension 文件扩展名。
     *
     * @return string 文件类型，可以是 "image"、"video" 或 "down"。
     */
    protected static function extensions(string $extension)
    {
        $image_extensions = [
            'jpg', 'jpeg', 'png', 'gif', 'bmp',
            'tiff', 'tif', 'webp', 'ico', 'svg',
            'psd', 'jpg2', 'jpx', 'cr2'
        ];
        $video_extensions = [
            'mp4', 'avi', 'flv', 'mpg', 'mpeg',
            'wmv', 'mov', 'mkv', 'webm', 'ogv',
            '3gp', '3g2', 'rm', 'rmvb', 'vob',
            'm4v', 'm4a', 'mp3', 'f4v', 'f4a',
            'm2ts', 'ts', 'mts', 'divx', 'xvid'
        ];
    
        if (in_array($extension, $image_extensions)) {
            return "image";
        } elseif (in_array($extension, $video_extensions)) {
            return "video";
        } else {
            return "down";
        }
    }
    
    /**
     * 递归移除多维数组中的空数组元素。
     *
     * @param array $array 要处理的多维数组
     */
    protected static function removeEmptyArrays(&$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                self::removeEmptyArrays($value);
    
                // 如果当前数组为空，则移除该元素
                if (empty($value)) {
                    unset($array[$key]);
                }
            }
        }
    }
    
    /**
     * 去除字符串中的反斜杠
     *
     * @param mixed $string 需要处理的字符串或数组
     * @return mixed 处理后的字符串或数组
     */
    protected static function new_stripslashes($string) {
        if (!is_array($string)) return stripslashes($string);
        foreach ($string as $key => $val) {
            $string[$key] = self::new_stripslashes($val);
        }
        return $string;
    }
    
    /**
     * 将数组转换为字符串
     *
     * @param array $data 需要转换的数组
     * @param int $isformdata 是否处理表单数据，默认为 1
     * @return string 转换后的字符串
     */
    protected static function array2string(array $data, int $isformdata = 1) {
        if (empty($data)) return '';
    
        if ($isformdata) $data = self::new_stripslashes($data);
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            return addslashes(json_encode($data));
        } else {
            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        }
    }
    
    /**
     * 将字符串转换为数组
     *
     * @param string $data 要转换的字符串
     * @return array 转换后的数组
     * @author zhaosong
     */
    protected static function string2array(string $data) {
        $data = trim($data);
        if (empty($data)) return array();
    
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $data = stripslashes($data);
        }
        $array = json_decode($data, true);
        return is_array($array) ? $array : array();
    }
    
    /**
     * 截取字符串
     *
     * @param string $string 要截取的字符串
     * @param int $length 指定的截取长度
     * @return string 截取后的字符串
     * @author zhaosong
     */    
    protected static function truncateString(string $string, int $length)
    {
        // 过滤HTML标签
       $string = strip_tags($string);
      // 如果字符串长度小于等于指定长度，直接返回原字符串
      if (strlen($string) <= $length) {
        return $string;
      }
      $truncated = substr($string, 0, $length);
      // 添加省略号
      $truncated .= '...';
      return $truncated;
    }
    
    /**
     * 检查字符串中是否包含某些字符串
     * @param string       $haystack
     * @param string|array $needles
     * @return bool
     */
    protected static function contains(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' != $needle && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * 检查字符串是否以某些字符串结尾
     *
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    protected static function endsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === static::substr($haystack, -static::length($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串开头
     *
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    protected static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' != $needle && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * 获取指定长度的随机字母数字组合的字符串
     *
     * @param  int $length
     * @param  int $type
     * @param  string $addChars
     * @return string
     */
    protected static function random(int $length = 6, int $type = null, string $addChars = ''): string
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            default:
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($length > 10) {
            $chars = $type == 1 ? str_repeat($chars, $length) : str_repeat($chars, 5);
        }
        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $length);
        } else {
            for ($i = 0; $i < $length; $i++) {
                $str .= mb_substr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
            }
        }
        return $str;
    }
    
    /**
     * 转为首字母大写的标题格式
     *
     * @param  string $value
     * @return string
     */
    protected static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
    
    /**
     * 获取SQL实例对象的方法。
     * 
     * @return Sql 返回一个新的Sql实例对象。
     * 
     * @author zhaosong
     */
    protected static function sql()
    {
        return new Sql();
    }

}