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
namespace qhphp\lang;
/**
 * 多语言处理类
 */
class Lang
{
    private $lang = [];

    private $defaultLang ;

    /**
     * 构造函数
     *
     * @param string $defaultLang 默认语言，默认为'zh-cn'
     */
    public function __construct()
    {
        $this->defaultLang = C('default_lang');
    }

    /**
     * 加载语言包
     *
     * @param string $langFile 语言文件名
     * @param string $langPath 语言文件路径，默认为'./lang/'
     * @param string $range 语言范围，默认为空
     * @return $this
     */
    public function load($langPath = './lang/')
    {
        if (empty($range)) {
            $range = $this->defaultLang;
        }

        $file = $langPath . $range . '.php';

        if (is_file($file)) {
            $this->lang = include $file;
        }

        return $this;
    }

    /**
     * 获取语言变量
     *
     * @param string $name 语言变量名
     * @param string $range 语言范围，默认为空
     * @return mixed|string 返回对应的语言变量值，如果不存在则返回$name
     */
    public function get($name, $range = '')
    {
        if (empty($range)) {
            $range = $this->defaultLang;
        }
        
        if($this->defaultLang == 'zh-cn'){
            return isset($this->lang[$name]) ? $this->lang[$name] : $name;
        }
        
        else {
            return $name;
        }
    }

    /**
     * 设置当前语言
     *
     * @param string $lang 语言代码
     */
    public function setLang($lang)
    {
        $this->defaultLang = $lang;
    }

    /**
     * 自动侦测浏览器语言
     */
    public function detectLang()
    {
        $lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        preg_match('/^([a-z\-]+)/i', $lang, $matches);
        $lang = isset($matches[1]) ? $matches[1] : $this->defaultLang;
        $this->setLang($lang);
    }
}