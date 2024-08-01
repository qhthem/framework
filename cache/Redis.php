<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] Redis缓存类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\cache;
use Redis as Rs;

/**
 * RedisCache 类.
 *
 */
class Redis
{
    private $redis;

    /**
     * 构造函数.
     *
     * @param string $host     Redis 主机
     * @param int    $port     Redis 端口
     * @param string $password Redis 密码
     * @param int    $database Redis 数据库
     */
    public function __construct()
    {
        $this->redis = new Rs();
        $stores = C('stores')[C('cache_default')];
        
        $this->redis->connect($stores['host'], (int)$stores['port']);
        $password = $stores['password'];

        if (!empty($password)) {
            $this->redis->auth($password);
        }

        $this->redis->select($stores['database']);
    }

    /**
     * 将一个值存储到缓存中.
     *
     * @param string $key    缓存键
     * @param mixed  $value  缓存值
     * @param int    $expire 过期时间，单位为秒
     *
     * @return bool
     */
    public function set($key, $value, $expire = null)
    {
        if (is_null($expire)) {
            return $this->redis->set($key, serialize($value));
        }

        return $this->redis->setex($key, $expire, serialize($value));
    }

    /**
     * 从缓存中获取一个值.
     *
     * @param string $key 缓存键
     *
     * @return mixed
     */
    public function get($key)
    {
        $result = $this->redis->get($key);

        if ($result !== false) {
            return unserialize($result);
        }

        return $result;
    }

    /**
     * 从缓存中删除一个值.
     *
     * @param string $key 缓存键
     *
     * @return int
     */
    public function delete($key)
    {
        return $this->redis->del($key);
    }

    /**
     * 清空缓存.
     *
     * @return bool
     */
    public function clearCache()
    {
        return $this->redis->flushDB();
    }
}