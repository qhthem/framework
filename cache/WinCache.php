<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] WinCache缓存类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\cache;
use qhphp\exception\Exception;

class WinCache {
    /**
     * 缓存前缀
     * @var string
     */
    private $cache_prefix = 'qh_cache_';

    /**
     * 构造函数
     * @param string $cache_prefix 缓存前缀
     * @throws Exception 如果 WinCache 扩展未加载，则抛出异常
     */
    public function __construct($cache_prefix = '') {
        if (!extension_loaded('wincache')) {
            throw new Exception('WinCache 扩展未加载。');
        }

        if (!empty($cache_prefix)) {
            $this->cache_prefix = $cache_prefix;
        }
    }

    /**
     * 设置缓存
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $ttl 缓存有效期（秒），默认为 0（永久有效）
     * @return bool 成功返回 true，失败返回 false
     */
    public function set($key, $value, $ttl = 0) {
        $cache_key = $this->cache_prefix . $key;
        return wincache_ucache_set($cache_key, $value, $ttl);
    }

    /**
     * 获取缓存
     * @param string $key 缓存键
     * @return mixed 成功返回缓存值，失败返回 false
     */
    public function get($key) {
        $cache_key = $this->cache_prefix . $key;
        return wincache_ucache_get($cache_key);
    }

    /**
     * 删除缓存
     * @param string $key 缓存键
     * @return bool 成功返回 true，失败返回 false
     */
    public function delete($key) {
        $cache_key = $this->cache_prefix . $key;
        return wincache_ucache_delete($cache_key);
    }

    /**
     * 检查缓存是否存在
     * @param string $key 缓存键
     * @return bool 存在返回 true，不存在返回 false
     */
    public function exists($key) {
        $cache_key = $this->cache_prefix . $key;
        return wincache_ucache_exists($cache_key);
    }

    /**
     * 清除所有缓存
     * @return bool 成功返回 true，失败返回 false
     */
    public function clear() {
        return wincache_ucache_clear();
    }
}