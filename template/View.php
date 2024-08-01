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
namespace qhphp\template;
use qhphp\template\Template;
use Exception;

/**
 * 视图类，负责处理视图相关的操作
 * @author zhaosong
 */
class View
{
    /** @var string 应用名称 */
    public $app;
    /** @var string HTML文件名 */
    public $html;
    /** @var string 主题名称 */
    public $theme;
    /** @var string 缓存目录路径 */
    public $cacheDirectory;
    /** @var string 缓存文件路径 */
    public $cacheFile;
    /** @var string 模板文件路径 */
    public $templateFile;
    /** @var string 基础路径 */
    public $BasePath;

    /**
     * 构造函数，初始化视图类
     * @param string $app 应用名称
     * @param string $html HTML文件名
     * @author zhaosong
     */
    public function __construct(string $app, string $html)
    {
        $this->BasePath = App()::getBasePath();
        $this->app($app);
        $this->html = $html;
    }

    /**
     * 设置应用名称
     * @param string $app 应用名称
     * @return View 返回当前视图实例
     * @author zhaosine
     */
    public function app(string $app)
    {
        $app = !empty($app) ? $app : Router()->formatAppnName();
        $this->app = $app;
        return $this;
    }

    /**
     * 获取主题名称
     * @return View 返回当前视图实例
     * @author zhaosong
     */
    public function theme()
    {
        $theme_file = app()::getThemPath($this->app);
        $theme = Cget($theme_file, 'theme');
        $this->theme = $theme;
        return $this;
    }

    /**
     * 设置缓存目录路径
     * @return View 返回当前视图实例
     * @author zhaosong
     */
    public function cacheDirectory()
    {
        $cacheDirectory = App()::getRuntimePath() . $this->app;

        if (!file_exists($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }
        $this->cacheDirectory = $cacheDirectory;
        return $this;
    }

    /**
     * 设置模板文件路径
     * @return View 返回当前视图实例
     * @author zhaosong
     */
    public function templateFile()
    {
        $templateFile = $this->BasePath
            . $this->app . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR
            . (!empty($this->theme) ? ($this->theme . DIRECTORY_SEPARATOR) : '')
            . $this->html . C('view_suffix');

        $this->templateFile = $templateFile;
        return $this;
    }

    /**
     * 设置缓存文件路径
     * @return View 返回当前视图实例
     * @throws Exception 如果模板文件不存在
     * @author zhaosong
     */
    public function cacheFile()
    {
        $cacheFile = $this->cacheDirectory . '/' . md5($this->html) . '.php';
        $templateFile = $this->templateFile;

        if (!file_exists($templateFile)) {
            throw new Exception(Lang('template not exists') . ": " . $templateFile, 404);
        }

        $this->cacheFile = $cacheFile;
        return $this;
    }

    /**
     * 获取模板内容
     * @return string 缓存文件路径
     * @author zhaosong
     */
    public function template()
    {
        $cacheFile = $this->cacheFile;
        $templateFile = $this->templateFile;
        if (!file_exists($cacheFile) || filemtime($templateFile) > filemtime($cacheFile)) {
            $content = file_get_contents($templateFile);
            $compiled = (new Template())->parseTag($content);
            file_put_contents($cacheFile, $compiled);
        }

        return $cacheFile;
    }
}