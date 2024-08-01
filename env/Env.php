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
namespace qhphp\env;

/**
 * Env 类用于加载和管理环境变量。
 */
class Env
{
    /**
     * 文件路径
     *
     * @var string
     */
    private $filePath;

    /**
     * 构造函数
     *
     * @param string $filePath 文件路径
     */
    public function __construct()
    {
        $this->filePath = ROOT_PATH.'astro.env';
    }

    /**
     * 创建环境变量
     *
     * @param string $key   环境变量的键
     * @param string $value 环境变量的值
     *
     * @author zhaosong
     */
    public function create($value)
    {
        if (!file_exists($this->filePath)){
            touch($this->filePath);
        }
        $envFile = "";
        foreach ($value as $key => $val) {
            $envFile .= $key . "=" . $val . "\n";
        }
        
        file_put_contents($this->filePath, $envFile);
    }
    
    /**
     * 向 .env 文件中添加一行
     *
     * @param array $data 要添加的键值对数组，格式为 ['key' => 'value']
     * @return void
     * @author zhaosong
     */
    public function add($data)
    {
        // 读取 .env 文件的内容
        $envFile = file_get_contents($this->filePath);
    
        // 遍历数组，将每个键值对添加到文件末尾
        foreach ($data as $key => $value) {
            $envFile .= $key . "=" . $value . "\n";
        }
    
        // 将更新后的内容写回到 .env 文件中
        file_put_contents($this->filePath, $envFile);
    }

    /**
     * 读取环境变量
     *
     * @param string $key 环境变量的键
     *
     * @return string|null 环境变量的值，如果不存在则返回 null
     *
     * @author zhaosong
     */
    public function read($key)
    {
        $envFile = file_get_contents($this->filePath);
        $envFile = explode("\n", $envFile);
    
        $envVars = [];
        foreach ($envFile as $line) {
            $line = explode("=", $line);
            if (count($line) == 2) {
                $envVars[$line[0]] = $line[1];
            }
        }
    
        return $envVars[$key] ?? null;
    }

    /**
     * 删除环境变量
     *
     * @param string $key 环境变量的键
     *
     * @author zhaosong
     */
    public function delete($key)
    {
        $envFile = file_get_contents($this->filePath);
        $envFile = explode("\n", $envFile);
    
        $newEnvFile = "";
        foreach ($envFile as $line) {
            $line = explode("=", $line);
            if (count($line) == 2 && $line[0] != $key) {
                $newEnvFile .= $line[0] . "=" . $line[1] . "\n";
            }
        }
    
        file_put_contents($this->filePath, $newEnvFile);
    }

}