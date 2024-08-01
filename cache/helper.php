<?php
// +----------------------------------------------------------------------
// | 缓存助手函数
// +----------------------------------------------------------------------
use qhphp\cache\File;
use qhphp\cache\Files;


if (!function_exists('Cache')) {
    
    /**
     * 获取缓存类型
     *
     * @return mixed
     */
    function Cache()
    {
        $tyep_cache = "qhphp\\cache\\".C('cache_default');
        $cache = new $tyep_cache();
        return $cache;
    }
}

if (!function_exists('cache_type')) {
    
    /**
     * 获取缓存类型
     *
     * @return mixed
     */
    function cache_type()
    {
        $tyep_cache = "qhphp\\cache\\Files";
        $cache = new $tyep_cache();
        return $cache;
    }
}


/**
 * 从缓存中获取数据，如果缓存中没有数据，则执行查询并将结果存入缓存。
 *
 * @param string $cacheKey 缓存键名
 * @param callable $queryFunc 查询函数，用于执行查询并返回结果
 * @param int $expireTime 缓存过期时间，默认为3600秒
 * @return mixed 查询结果
 * @author zhaosong
 */
if (!function_exists('cache_get_or_set')) {
    function cache_get_or_set($cacheKey, $queryFunc, $expireTime = 3600) {
        // 尝试从缓存中获取数据
        $data = Cache()->get($cacheKey);

        // 如果缓存中没有数据，则执行查询并将结果存入缓存
        if (empty($data)) {
            $data = call_user_func_array($queryFunc, []);
            Cache()->set($cacheKey, $data, $expireTime);
        }

        return $data;
    }
}
