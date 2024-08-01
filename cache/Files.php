<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] 文件文本缓存类
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
 * 文件缓存类
 */
class Files
{
    // 缓存目录
    private $cacheDir;

    // 缓存文件后缀
    private $cacheFileSuffix = '.cache.php';
    
    private $cacheExpiration;

    // 缓存文件内容格式
    private $cacheFileContent = "<?php\n\n// +----------------------------------------------------------------------\n// | 缓存格式\n// +----------------------------------------------------------------------\n\nreturn [\n%s\n];\n";
    
    // 缓存文件内容格式
    private $cacheFileArray = "<?php\n\n// +----------------------------------------------------------------------\n// | 缓存格式\n// +----------------------------------------------------------------------\n\nreturn (\n%s\n);\n";

    public function __construct()
    {
        // 获取缓存文件路径
        $stores = C('stores')[C('cache_default')];
        $this->cacheDir = $stores['path'];
        
        $this->cacheExpiration = $stores['expire'];
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * 设置缓存
     *
     * @param string $key 键名
     * @param mixed $value 键值
     * @return bool
     */
    public function set($key, $value, $var_export = false)
    {
        $cacheFile = $this->getCacheFile($key);
        $content = sprintf($var_export ? $this->cacheFileArray:$this->cacheFileContent, $var_export ? var_export_to_html($value):
            $this->formatArray($value));
        return file_put_contents($cacheFile, $content);
    }
    
    /**
     * 获取缓存
     *
     * @param string $key 键名
     * @return mixed
     */
    public function get($key)
    {
        $cacheFile = $this->getCacheFile($key);
        
        if($this->cacheExpiration == 0){
            if (file_exists($cacheFile)) {
                return require $cacheFile;
            }
        }
        
        if (time() < $this->cacheExpiration) {
            if (file_exists($cacheFile)) {
                return require $cacheFile;
            }
        } else {
            if (file_exists($cacheFile)){
                unlink($cacheFile);
            }
        }
        
        return false;
    }

    /**
     * 获取缓存文件路径
     *
     * @param string $key 键名
     * @return string
     */
    private function getCacheFile($key)
    {
        return $this->cacheDir . md5($key) . $this->cacheFileSuffix;
    }

    /**
     * 格式化数组为字符串
     *
     * @param array $array 数组
     * @return string
     */
    private function formatArray($array)
    {
        $formattedArray = [];
        foreach ($array as $key => $value) 
        {
            if(is_array($value)){
                foreach ($value as $key => $val) {
                    $formattedArray[] = sprintf("    '%s' => '%s',", $key, $val);
                }
            }
            else{
                $formattedArray[] = sprintf("    '%s' => '%s',", $key, $value);
            }
            
        }
        return implode("\n\n", $formattedArray);
    }
    
    /**
     * 清除缓存
     *
     * @return void
     */
    public function clearCache()
    {
        $files = glob($this->cacheDir . '/*.php');
        if(!empty($files)){
            foreach ($files as $file) {
                unlink($file);
            }
        }
        
        return true;
    }

    /**
     * 删除缓存
     *
     * @param string $key 键名
     * @return void
     */
    public function delCache($key)
    {
        $files = $this->getCacheFile($key);
        if(is_file($files)){
            unlink($files);
            return true;
        }
    }
}