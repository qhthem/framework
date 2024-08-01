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
namespace qhphp\page;

class Pagination
{
    /**
     * 总记录数
     *
     * @var int
     */
    private $total;

    /**
     * 每页显示的记录数
     *
     * @var int
     */
    private $pageSize;

    /**
     * 当前页码
     *
     * @var int
     */
    private $currentPage;

    /**
     * 当前页面的URL
     *
     * @var string
     */
    private $url;

    /**
     * 自定义参数
     *
     * @var array
     */
    private $params;

    /**
     * 构造函数
     *
     * @param int $total 总记录数
     * @param int $pageSize 每页显示的记录数
     * @param int $currentPage 当前页码
     * @param string $url 当前页面的URL
     * @param array $params 自定义参数
     */
    public function __construct($total, $pageSize, $currentPage, $url, $params = [],$prefix = 'list_',$url_rule = true)
    {
        $this->total = $total;
        $this->pageSize = $pageSize;
        $this->currentPage = $currentPage;
        $this->url = $url;
        $this->params = $params;
        $this->parameter  = empty($params) ? $_GET : $params;
        $this->page_prefix = $prefix;
        $this->url_rule = !empty($url_rule) ? $url_rule : false;
    }

    /**
     * 渲染分页
     *
     * @return string 分页HTML代码
     */
    public function render()
    {
        $totalPages = ceil($this->total / $this->pageSize); // 总页数

        $html = '';

        if ($totalPages > 1) {
            $html .= '<ul class="pagination">';

            // 首页
            if ($this->currentPage > 1) {
                $html .= '<li><a href="' . $this->getUrl(1) . '">'.Lang('home_page' ).'</a></li>';
            }

            // 上一页
            if ($this->currentPage > 1) {
                $html .= '<li><a href="' . $this->getUrl($this->currentPage - 1) . '">'.Lang('pre_page' ).'</a></li>';
            }

            // 数字列表页
            $startPage = max(1, $this->currentPage - 2);
            $endPage = min($startPage + 4, $totalPages);

            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($i == $this->currentPage) {
                    $html .= '<li class="active"><span>' . $i . '</span></li>';
                } else {
                    $html .= '<li><a href="' . $this->getUrl($i) . '">' . $i . '</a></li>';
                }
            }
            

            // 下一页
            if ($this->currentPage < $totalPages) {
                $html .= '<li><a href="' . $this->getUrl($this->currentPage + 1) . '">'.Lang('next_page' ).'</a></li>';
            }

            // 末页
            if ($this->currentPage < $totalPages) {
                $html .= '<li><a href="' . $this->getUrl($totalPages) . '">'.Lang('end_page' ).'</a></li>';
            }

            $html .= '</ul>';
        }

        return $html;
    }
    
    /**
     * 获取分页URL
     *
     * @author zhaosong
     * @return string 分页URL
     */
    protected function url()
    {
        unset($this->parameter['app'], $this->parameter['controller'], $this->parameter['action']);
        $this->parameter['page'] = 'PAGE';
    
        if ($this->url_rule) {
            return $this->_list_url();
        }
    
        return url(Router()->formatAppnName(), $this->parameter);
    }
    
    /**
     * 获取自定义分页URL
     *
     * @author zhaosong
     * @return string 分页URL
     */
    private function _list_url()
    {
        $parameter = '';
        $request_url = trim(str_replace([C('view_suffix'), $this->page_prefix . $this->currentPage], '', $_SERVER['REQUEST_URI']), '/');
    
        // 支持传入自定义参数  ?aa=1&bb=2
        $pos = strpos($request_url, '?');
        if ($pos !== false) {
            list($request_url, $parameter) = explode('?', $request_url);
            if ($parameter) {
                parse_str($parameter, $vars);
                $parameter = '?' . http_build_query($vars);
            }
            $request_url = trim($request_url, '/');
        }
    
        if ($request_url) $request_url .= '/';
    
        return SITE_URL() . $request_url . $this->page_prefix . 'PAGE' . C('view_suffix') . $parameter;
    }
	
    /**
     * 获取指定页面的URL
     *
     * @param int $page 页码
     * @return string 指定页面的URL
     */
    private function getUrl($page)
    {
        $url = str_replace('PAGE', $page, $this->url());
        return $url;
    }
}