<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] 错误输出处理类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\debug;
use qhphp\request\Request;
/**
 * Html类，用于处理调试信息的输出
 * @author zhaosong
 */
class Html
{
    public static $stoptime; // 记录停止时间的变量
    public static $info = []; // 存储普通信息的数组
    public static $sqls = []; // 存储SQL信息的数组
    public static $request = []; // 存储请求信息的数组
    public static $msg = []; // 存储消息的数组
    

    /**
     * 停止计时
     * @author zhaosong
     */
    public static function stop()
    {
        self::$stoptime = microtime(true); // 记录当前时间作为停止时间
    }

    /**
     * 计算花费的时间
     * @return float 花费的时间（秒）
     * @author zhaosong
     */
    public static function spent()
    {
        return round((self::$stoptime - microtime(true)), 4); // 返回从开始到停止的时间差，保留四位小数
    }

    /**
     * 添加调试信息
     * @param string $msg 调试信息
     * @param int $type 信息类型，默认为0（普通信息）
     * @param float $start_time 开始时间，默认为0
     * @author zhaosone
     */
    public static function addmsg($msg, $type = 0, $start_time = 0)
    {
        switch ($type) {
            case 0:
                self::$info[] = $msg; // 添加普通信息
                break;
            case 1:
                self::$sqls[] = htmlspecialchars($msg) . '; [ RunTime:' . number_format(microtime(true) - $start_time, 6) . 's ]'; // 添加SQL信息，并附带运行时间
                break;
            case 2:
                self::$request[] = $msg; // 添加请求信息
                break;
        }
    }
    
    /**
     * 获取已包含的文件列表。
     * 
     * @param bool $detail 是否返回详细信息的文件列表，默认为false，只返回文件数量。
     * @return int|array 如果$detail为false，返回文件数量；如果为true，返回一个包含文件路径及其大小（KB）的数组。
     * @author zhaosong
     */
    public static function getFile($detail = false)
    {
        // 获取已包含的文件列表
        $files = get_included_files();
    
        // 如果需要详细信息
        if ($detail) {
            // 初始化信息数组
            $info = [];
    
            // 遍历文件列表
            foreach ($files as $file) {
                // 将文件路径和大小（KB）添加到信息数组中
                $info[] = $file . ' ( ' . number_format(filesize($file) / 1024, 2) . ' KB )';
            }
    
            // 返回信息数组
            return $info;
        }
    
        // 否则返回文件数量
        return count($files);
    }

    /**
     * 输出调试信息
     * @author zhaosong
     */
    public static function debugMessage()
    {
        $parameter = Request()::get();
        unset($parameter['m'], $parameter['c'], $parameter['a']); 
        $parameter = $parameter ? http_build_query($parameter) : '无'; // 如果存在其他GET参数，则构建查询字符串，否则设为'无'
        $files = self::getFile(true); // 获取所有已包含的文件
        ob_start(); // 开启输出缓冲
        include app()::getQhphpPath() . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'debug_messages.tpl'; // 包含调试信息模板文件
    }
}
