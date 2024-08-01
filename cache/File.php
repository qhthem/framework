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
namespace qhphp\cache;

use Exception;
/**
 * Files类，用于处理文件缓存相关操作。
 * @author zhaosong
 */
class File 
{
    /**
     * 配置选项。
     * @var array
     */
    protected $options = [
        'expire'        => 0, // 缓存过期时间，默认为0，表示永不过期。
        'cache_subdir'  => true, // 是否使用子目录存储缓存文件。
        'prefix'        => '', // 缓存文件的前缀。
        'path'          => '', // 缓存文件的存储路径。
        'data_compress' => false, // 是否对缓存数据进行压缩。
    ];

    /**
     * 缓存过期时间。
     * @var int
     */
    protected $expire;
    
    /**
     * 构造函数，初始化配置选项和缓存路径。
     * @param array $options 自定义配置选项。
     */
    public function __construct($options = [])
    {
        $stores = C('stores')[C('cache_default')];
        
        $this->options['path'] = $stores['path'];
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if (substr($this->options['path'], -1) != DIRECTORY_SEPARATOR) {
            $this->options['path'] .= DIRECTORY_SEPARATOR;
        }
        $this->init();
    }
    
    /**
     * 初始化缓存目录。
     * @return bool 成功创建目录返回true，否则返回false。
     */
    private function init()
    {
        // 创建项目缓存目录
        if (!is_dir($this->options['path'])) {
            if (mkdir($this->options['path'], 0755, true)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 生成缓存文件的键名。
     * @param string $name 缓存键名。
     * @param bool $auto 是否自动创建目录。
     * @return string 缓存文件的完整路径。
     */
    protected function getCacheKey($name, $auto = false)
    {
        // 对缓存键名进行MD5加密，生成唯一标识符。
        $name = md5($name);
        
        // 根据配置选项，判断是否使用子目录存储缓存文件。
        if ($this->options['cache_subdir']) {
            // 使用子目录
            $name = substr($name, 0, 2) . DIRECTORY_SEPARATOR . substr($name, 2);
        }
        
        // 如果有设置缓存文件前缀，则添加到文件名前。
        if ($this->options['prefix']) {
            $name = $this->options['prefix'] . DIRECTORY_SEPARATOR . $name;
        }
        
        // 拼接缓存文件的完整路径。
        $filename = $this->options['path'] . $name . '.php';
        $dir      = dirname($filename);

        // 如果需要自动创建目录且目录不存在，则创建目录。
        if ($auto && !is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // 返回缓存文件的完整路径。
        return $filename;
    }
    
    /**
     * 检查缓存是否存在。
     * @param string $name 缓存键名。
     * @return bool 缓存存在返回true，否则返回false。
     */
    public function has($name)
    {
        // 通过调用get方法获取缓存值，如果获取到值则表示缓存存在，返回true；否则返回false。
        return $this->get($name) ? true : false;
    }

    /**
     * 获取缓存值。
     * @param string $name 缓存键名。
     * @param mixed $default 缓存不存在时的默认值。
     * @return mixed 缓存值或默认值。
     */
    public function get($name, $default = false)
    {
        // 通过getCacheKey方法获取缓存文件的完整路径。
        $filename = $this->getCacheKey($name);
        
        // 如果缓存文件不存在，则返回默认值。
        if (!is_file($filename)) {
            return $default;
        }
        
        // 读取缓存文件内容。
        $content = file_get_contents($filename);
        $this->expire = null;
        
        // 如果读取内容成功，则解析过期时间和压缩数据。
        if (false !== $content) {
            // 解析过期时间。
            $expire = (int) substr($content, 8, 12);
            
            // 如果缓存已过期，则返回默认值。
            if (0 != $expire && time() > filemtime($filename) + $expire) {
                return $default;
            }
            
            // 保存过期时间。
            $this->expire = $expire;
            
            // 解析压缩数据。
            $content = substr($content, 32);
            
            // 如果启用数据压缩且函数存在，则解压数据。
            if ($this->options['data_compress'] && function_exists('gzcompress')) {
                //启用数据压缩
                $content = gzuncompress($content);
            }
            
            // 反序列化数据并返回。
            $content = unserialize($content);
            return $content;
        } else {
            // 读取内容失败，则返回默认值。
            return $default;
        }
    }
    
    /**
     * 设置缓存值。
     * @param string $name 缓存键名。
     * @param mixed $value 缓存值。
     * @param int|null $expire 缓存过期时间，默认为null，表示使用配置文件中的默认过期时间。
     * @return bool 设置成功返回true，否则返回false。
     */
    public function set($name, $value, $expire = null)
    {
        // 如果没有指定过期时间，则从配置文件中获取默认过期时间。
        if (is_null($expire)) {
            $stores = C('stores')[C('cache_default')];
            $expire = $stores['expire'];
        }
        
        // 如果过期时间是一个DateTime对象，则将其转换为时间戳。
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        
        // 通过getCacheKey方法获取缓存文件的完整路径，并确保目录存在。
        $filename = $this->getCacheKey($name, true);
        
        // 序列化缓存值。
        $data = serialize($value);
        
        // 如果启用数据压缩且函数存在，则压缩数据。
        if ($this->options['data_compress'] && function_exists('gzcompress')) {
            //数据压缩
            $data = gzcompress($data, 3);
        }
        
        // 将过期时间和压缩后的数据拼接成PHP文件内容格式。
        $data   = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $data;
        
        // 将数据写入缓存文件。
        $result = file_put_contents($filename, $data);
        
        // 如果写入成功，则返回true；否则返回false。
        if ($result) {
            isset($first) && $this->setTagItem($filename);
            clearstatcache();
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 增加缓存值
     * @param string $name 缓存键名
     * @param int $step 增加的步长，默认为1
     * @return bool|int 返回增加后的值，如果设置失败则返回false
     * @author zhaosong
     */
    public function inc($name, $step = 1)
    {
        if ($this->has($name)) {
            $value  = $this->get($name) + $step; // 获取当前值并加上步长
            $expire = $this->expire; // 获取过期时间
        } else {
            $value  = $step; // 如果没有当前值，则初始化为步长值
            $expire = 0; // 没有过期时间
        }
    
        return $this->set($name, $value, $expire) ? $value : false; // 设置新值并返回，如果设置失败则返回false
    }
    
    /**
     * 减少缓存值
     * @param string $name 缓存键名
     * @param int $step 减少的步长，默认为1
     * @return bool|int 返回减少后的值，如果设置失败则返回false
     * @author zhaosong
     */
    public function dec($name, $step = 1)
    {
        if ($this->has($name)) {
            $value  = $this->get($name) - $step; // 获取当前值并减去步长
            $expire = $this->expire; // 获取过期时间
        } else {
            $value  = -$step; // 如果没有当前值，则初始化为负步长值
            $expire = 0; // 没有过期时间
        }
    
        return $this->set($name, $value, $expire) ? $value : false; // 设置新值并返回，如果设置失败则返回false
    }
    
    /**
     * 删除缓存
     * @param string $name 缓存键名
     * @return bool 删除成功返回true，否则返回false
     * @author zhaosong
     */
    public function del($name)
    {
        $filename = $this->getCacheKey($name); // 获取缓存文件名
        try {
            return $this->unlink($filename); // 尝试删除文件并返回结果
        } catch (\Exception $e) {
            // 捕获异常，但不处理，直接返回false
        }
    }

    /**
     * 清空缓存目录下的所有缓存文件
     * @return bool 清空成功返回true
     * @author zhaosong
     */
    public function clear()
    {
        // 获取缓存目录下所有文件和目录
        $files = (array) glob($this->options['path'] . ($this->options['prefix'] ? $this->options['prefix'] . DIRECTORY_SEPARATOR : '') . '*');
        
        // 遍历文件和目录
        foreach ($files as $path) {
            if (is_dir($path)) { // 如果是目录
                $matches = glob($path . '/*.php'); // 获取目录下所有php文件
                if (is_array($matches)) { // 如果存在文件
                    array_map('unlink', $matches); // 删除文件
                }
                rmdir($path); // 删除目录
            } else { // 如果是文件
                unlink($path); // 删除文件
            }
        }
        
        return true; // 清空成功
    }
    
    /**
     * 安全地删除文件
     * @param string $path 文件路径
     * @return bool 删除成功返回true，否则返回false
     * @author zhaosong
     */
    private function unlink($path)
    {
        return is_file($path) && unlink($path); // 检查文件是否存在并删除
    }

}
