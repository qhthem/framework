<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] 错误日志写入类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\logs;
use qhphp\config\Config;
class Log
{
    
    
    /**
     * 配置数组.
     *
     * @var array
     */
    private static $config = [];

    /**
     * 日志级别.
     *
     * @var array
     */
    private static $levels = [
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7,
    ];

    /**
     * 日志数组.
     *
     * @var array
     */
    private static $logs = [];

    /**
     * 记录日志消息.
     *
     * @param string $message 日志消息
     * @param string $level 日志级别
     * @param array $context 日志上下文
     */
    public static function record($message, $level = 'info', $context = [])
    {
        if (self::$levels[$level] <= self::$levels[C('log_level')]) {
            self::$logs[] = [
                'message' => $message,
                'level' => $level,
                'context' => $context,
                'time' => date('Y-m-d H:i:s'),
            ];
            
            self::save();
        }
    }

    /**
     * 保存日志到文件.
     */
    public static function save()
    {
        if (!empty(self::$logs)) {
            // 检查日志目录是否存在，如果不存在则创建目录
            $logDir = C('log_path');
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }
             // 检查日志文件是否存在，如果不存在则创建文件
            $filename = C('log_path') . date('Ymd') . '.log';
            if (!file_exists($filename)) {
                touch($filename);
            }
            $content = '';
            foreach (self::$logs as $log) {
                $content .= '[' . $log['time'] . '] ' . strtoupper($log['level']) . ': ' . $log['message'] . PHP_EOL;
            }
            // 写入日志文件
            file_put_contents($filename, $content, FILE_APPEND);
            self::$logs = [];
        }
    }

    /**
     * 清除日志.
     */
    public static function clear()
    {
        $files = glob(C('log_path') . '*.log');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * 自动清除日志.
     */
    public static function autoClear()
    {
        if (C('log_auto_clear')) {
            $files = glob(C('log_path') . '*.log');
            if (count($files) >= C('log_max_files')) {
                self::clear();
            }
        }
    }

    /**
     * 析构函数.
     */
    public function __destruct()
    {
        self::save();
        self::autoClear();
    }
}