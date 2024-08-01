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
namespace qhphp\response;

/**
 * 响应处理类
 * @author zhaosong
 */
class Response
{
    /** @var string 默认内容类型 */
    protected $contentType = 'application/json';

    /** @var string 默认字符集 */
    protected $charset = 'utf-8';

    /** @var int 默认状态码 */
    protected $code = 200;

    /** @var array 请求头信息 */
    protected $header = [];

    /**
     * 构造函数
     * @param int $code HTTP状态码
     * @param array $header 请求头数组
     * @author zhaosong
     */
    public function __construct($code = 200, array $header = [])
    {
        $this->contentType($this->contentType, $this->charset);
        $this->header = array_merge($this->header, $header);
        $this->code = $code;
    }

    /**
     * 返回JSON格式的响应
     * @param mixed $data 要返回的数据
     * @author zhaosong
     */
    public function Json($data)
    {
        http_response_code($this->code);
        $this->response();
        header("X-Powered-By: QHPHP/QHTHEM.");
        $content = json_encode($data, JSON_UNESCAPED_UNICODE);
        echo($content);
        exit;
    }

    /**
     * 返回响应头信息
     * @author zhaosong
     */
    public function response()
    {
        foreach ($this->header as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    /**
     * 设置响应选项
     * @param array $options 选项数组
     * @return Response
     * @author zhaosong
     */
    public function options($options = [])
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * 设置响应头
     * @param string|array $name 头名称或头信息的关联数组
     * @param string|null $value 头值
     * @return Response
     * @author zhaosong
     */
    public function header($name, $value = null)
    {
        if (is_array($name)) {
            $this->header = array_merge($this->header, $name);
        } else {
            $this->header[$name] = $value;
        }
        return $this;
    }

    /**
     * 重定向到指定URL
     * @param string $url 目标URL
     * @return Response
     * @author zhaosian
     */
    public function redirect($url)
    {
        $this->header['Location'] = $url;
        return $this;
    }

    /**
     * 设置HTTP状态码
     * @param int $code HTTP状态码
     * @return Response
     * @author zhaosong
     */
    public function code($code)
    {
        $this->code = $code == 200 ? $code : 404;
        return $this;
    }

    /**
     * 设置内容类型和字符集
     * @param string $contentType 内容类型
     * @param string $charset 字符集
     * @return Response
     * @author zhaosong
     */
    public function contentType($contentType = '', $charset = 'utf-8')
    {
        if (empty($contentType)) {
            $contentType = $this->contentType;
        }
        $this->header['Content-Type'] = $contentType . '; charset=' . $charset;
        return $this;
    }
}