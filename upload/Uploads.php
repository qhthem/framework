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
namespace qhphp\upload;
use qhphp\config\Config;

class Uploads
{
    /**
     * 配置数组
     *
     * @var array
     */
    public $config = [];

    /**
     * 错误信息
     *
     * @var string
     */
    private $error = '';

    /**
     * 构造函数
     *
     * @param array $config 配置数组
     */
    public function __construct()
    {
        $config = [];
        if (empty($config)) {
            $this->config = [
                'size' => C('upload_size'),
                'ext' => C('upload_ext'),
                'path' => C('upload_path'),
                'is_name' => C('upload_is_name'),
            ];
        }
    }

    /**
     * 上传文件
     *
     * @param array $file 文件信息
     * @return string|bool 成功返回文件名，失败返回 false
     */
    public function upload($file)
    {
        // 检查文件大小
        if ($file['size'] > $this->config['size']) {
            return $this->getError(Lang('upload File size exceeds the maximum value' ));
        }

        // 检查文件后缀
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, explode(',', $this->config['ext']))) {
           return $this->getError(Lang('extensions to upload is not allowed' ));
        }

        // 生成文件名
        $filename = $this->config['is_name'] ? $file['name'] . '.' . $ext:md5(microtime(true)) . '.' . $ext;

        // 创建目录
        $dir = $this->config['path'];
        
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
         // 检查文件是否已经上传
        if (!is_uploaded_file($file['tmp_name'])) {
            return $this->getError(Lang('upload write error' ));
        }

        // 上传文件
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
            return $this->getError(Lang('upload write error' ));
        }
        
        return $this->getInfo(C('upload_path').$filename);
    }

    /**
     * 获取错误信息
     *
     * @return string 错误信息
     */
    public function getError($error)
    {
        return json(['status'=>0,'msg'=>$error]);
    }

    /**
     * 检查文件大小
     *
     * @param array $file 文件信息
     * @return bool 文件大小符合要求返回 true，否则返回 false
     */
    private function checkSize($file)
    {
        return $file['size'] <= $this->config['size'];
    }

    /**
     * 检查文件后缀
     *
     * @param array $file 文件信息
     * @return bool 文件后缀符合要求返回 true，否则返回 false
     */
    private function checkExt($file)
    {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($ext, explode(',', $this->config['ext']));
    }

    /**
     * 获取文件哈希值
     *
     * @param string $file 文件路径
     * @return string 文件哈希值
     */
    public function getHash($file)
    {
        return hash_file('md5', $file);
    }

    /**
     * 获取文件信息
     *
     * @param string $file 文件路径
     * @return array 文件信息数组
     */
    public function getInfo($file)
    {
        $info = [];
        $info['filename'] = str_replace('.'.pathinfo($file, PATHINFO_EXTENSION), "", pathinfo($file, PATHINFO_BASENAME));
        $info['originname'] = C('upload_getpath').str_replace(C('upload_path'), "", $file);
        $info['filesize'] = filesize($file);
        $info['ext'] = pathinfo($file, PATHINFO_EXTENSION);
        $info['arrtype'] = extensions(pathinfo($file, PATHINFO_EXTENSION));
        $info['uploadtime'] = filemtime($file);
        $info['hash'] = $this->getHash($file);
        return $info;
    }
    
    /**
     * 获取文件名
     *
     * @param string $file 文件路径
     * @return string 文件名
     */
    public function getFilename($file)
    {
        return pathinfo($file, PATHINFO_BASENAME);
    }
}
