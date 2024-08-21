<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] Cookie类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\cookie;
use qhphp\openssl\StringAuth;
class Cookie
{
    protected $path;
    protected $domain;
    protected $secure;
    protected $httponly;
    protected $characters = '';
    protected $expire = 0;
    protected $des = null;

    /**
     * 创建一个新的Cookie实例
     *
     * @param string $path     Cookie的路径
     * @param string $domain   Cookie的域
     * @param bool   $secure   是否仅通过安全连接传输Cookie
     * @param bool   $httponly 是否仅通过HTTP协议访问Cookie
     */
    public function __construct()
    {
        $this->path = C('cookie_path'); // 设置Cookie的路径
        $this->domain = is_null(C('cookie_domain')) ? '':C('cookie_domain'); // 设置Cookie的域
        $this->secure = is_null(C('cookie_secure')) ? false:true; // 设置是否仅通过安全连接传输Cookie
        $this->httponly = is_null(C('cookie_httponly')) ? false:true; // 设置是否仅通过HTTP协议访问Cookie
        $this->expire = C('cookie_expire'); // 设置cookie 保存时间
        $this->des = C('cookie_des'); // 设置cookie 加密
    }
    

    /**
     * 设置Cookie
     *
     * @param string $name   Cookie名称
     * @param string $value  Cookie值
     * @param int    $expire 过期时间（可选，默认为0，即会话结束时失效）
     */
    public function set($name, $value, $expire = 0)
    {
        $expire = !empty($expire) ? time() + intval($this->expire) : 0;
        $value = empty($this->des) ? $value :$this->encryptCookie($value);
        setcookie($name, $value, $expire, $this->path, $this->domain, $this->secure, $this->httponly);
        // 使用setcookie()函数设置Cookie，传入名称、值、过期时间、路径、域、安全标志和HTTP标志
    }

    /**
     * 检查Cookie是否存在
     *
     * @param string $name Cookie名称
     * @return bool
     */
    public function exists($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * 获取Cookie的值
     *
     * @param string $name Cookie名称
     * @return mixed|null
     */
    public function get($name)
    {
        if ($this->exists($name)) {
            $value = empty($this->des) ? $_COOKIE[$name] :$this->decryptCookie($_COOKIE[$name]);
            return $value;
        }
        return null;
    }

    /**
     * 保存Cookie数据，如果Cookie不存在，则设置Cookie
     *
     * @param string $name   Cookie名称
     * @param string $value  Cookie值
     * @param int    $expire 过期时间（可选，默认为0，即会话结束时失效）
     */
    public function save($name, $value, $expire = 0)
    {
        if (!$this->exists($name)) {
            $this->set($name, $value, $expire);
        }
    }

    /**
     * 永久保存Cookie数据，设置过期时间为10年
     *
     * @param string $name  Cookie名称
     * @param string $value Cookie值
     */
    public function saveForever($name, $value)
    {
        $this->set($name, $value, time() + (10 * 365 * 24 * 60 * 60)); // 10 years
    }

    /**
     * 删除Cookie
     *
     * @param string $name Cookie名称
     */
    public function delete($name)
    {
        if ($this->exists($name)) {
            unset($_COOKIE[$name]);
            setcookie($name, '', time() - 3600, $this->path, $this->domain, $this->secure, $this->httponly);
            // 删除Cookie，将其值设置为空字符串，并将过期时间设置为过去的时间
        }
    }

    /**
     * 加密Cookie
     *
     * @param $data
     * @param $key
     * @return string
     */
    public function encryptCookie($data)
    {
        $stringAuth = new StringAuth();
        $encodedString = $stringAuth->string_auth($data, 'ENCODE');
        
        return $encodedString;
    }
    
    /**
     * 解密Cookie
     *
     * @param $encryptedData
     * @param $key
     * @return string|false
     */
    public function decryptCookie($data)
    {
        $stringAuth = new StringAuth();
        $decodedString = $stringAuth->string_auth($data, 'DECODE');
        
        return $decodedString;
    }
}
