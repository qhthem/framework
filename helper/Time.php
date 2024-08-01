<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] 时间格式获取类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\helper;
/**
 * 时间格式获取类
 */
class Time {
    /**
     * 获取今天的开始和结束时间戳
     *
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function today() {
        $start = strtotime(date('Y-m-d 00:00:00'));
        $end = strtotime(date('Y-m-d 23:59:59'));
        return [$start, $end];
    }

    /**
     * 获取昨天的开始和结束时间戳
     *
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function yesterday() {
        $start = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
        $end = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
        return [$start, $end];
    }

    /**
     * 获取本周的开始和结束时间戳
     *
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function week() {
        $start = strtotime(date('Y-m-d 00:00:00', strtotime('this week')));
        $end = strtotime(date('Y-m-d 23:59:59', strtotime('this week +6 days')));
        return [$start, $end];
    }

    /**
     * 获取上周的开始和结束时间戳
     *
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function lastWeek() {
        $start = strtotime(date('Y-m-d 00:00:00', strtotime('last week')));
        $end = strtotime(date('Y-m-d 23:59:59', strtotime('last week +6 days')));
        return [$start, $end];
    }

    /**
     * 获取本月的开始和结束时间戳
     *
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function month() {
        $start = strtotime(date('Y-m-01 00:00:00'));
        $end = strtotime(date('Y-m-t 23:59:59'));
        return [$start, $end];
    }

    /**
     * 获取上月的开始和结束时间戳
     *
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function lastMonth() {
        $start = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
        $end = strtotime(date('Y-m-t 23:59:59', strtotime('-1 month')));
        return [$start, $end];
    }

    /**
     * 获取今年的开始和结束时间戳
     *
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function year() {
        $start = strtotime(date('Y-01-01 00:00:00'));
        $end = strtotime(date('Y-12-31 23:59:59'));
        return [$start, $end];
    }

    /**
     * 获取去年的开始和结束时间戳
     *
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function lastYear() {
        $start = strtotime(date('Y-01-01 00:00:00', strtotime('-1 year')));
        $end = strtotime(date('Y-12-31 23:59:59', strtotime('-1 year')));
        return [$start, $end];
    }

    /**
     * 获取指定天数前零点到现在的时间戳
     *
     * @param int $days 天数
     * @param bool $endOfYesterday 是否包含昨天晚上23:59:59
     * @return array 包含开始和结束时间戳的数组
     * @author zhaosong
     */
    public static function dayToNow($days, $endOfYesterday = false) {
        $start = strtotime(date('Y-m-d 00:00:00', strtotime("-$days days")));
        $end = $endOfYesterday ? strtotime(date('Y-m-d 23:59:59', strtotime('-1 day'))) : time();
        return [$start, $end];
    }

    /**
     * 获取指定天数前的时间戳
     *
     * @param int $days 天数
     * @return int 时间戳
     * @author zhaosong
     */
    public static function daysAgo($days) {
        return strtotime(date('Y-m-d H:i:s', strtotime("-$days days")));
    }

    /**
     * 获取指定天数后的时间戳
     *
     * @param int $days 天数
     * @return int 时间戳
     * @author zhaosong
     */
    public static function daysAfter($days) {
        return strtotime(date('Y-m-d H:i:s', strtotime("+$days days")));
    }

    /**
     * 天数转换成秒数
     *
     * @param int $days 天数
     * @return int 秒数
     * @author zhaosong
     */
    public static function daysToSecond($days) {
        return $days * 24 * 60 * 60;
    }

    /**
     * 周数转换成秒数
     *
     * @param int $weeks 周数
     * @return int 秒数
     * @author zhaosong
     */
    public static function weekToSecond($weeks) {
        return $weeks * 7 * 24 * 60 * 60;
    }
}
