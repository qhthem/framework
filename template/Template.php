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
use qhphp\app\App;
use qhphp\config\Config;


/**
 * @property string $templatePath 模板路径
 * @property string $cachePath 缓存路径
 * @property array $data 模板数据
 */
class Template
{

    /**
     *  构造方法
     *  @return 
     */	
    public function __construct() {
		
    }	
	

    /**
     * 解析模板标签
     *
     * @param string $str 待解析的字符串
     * @return string 解析后的字符串
     */
    public function parseTag($str) 
    {
        $begin = C('tpl_begin');
	    $end = C('tpl_end');
	    
	    foreach (C('tpl_replace_string') as $rule => $key) {
            $str = preg_replace("/".$begin . $rule . $end."/", $key, $str);
        }
        
        $str = $this->parseInclude($str,$begin,$end);
        $str = $this->parsePhp($str,$begin,$end);
        $str = $this->parseIf($str,$begin,$end);
        $str = $this->parseElseif($str,$begin,$end);
        $str = $this->parseElse($str,$begin,$end);
        $str = $this->parseEndif($str,$begin,$end);
        $str = $this->parseFor($str,$begin,$end);
        $str = $this->parseEndfor($str,$begin,$end);
        $str = $this->parseInc($str,$begin,$end);
        $str = $this->parseDec($str,$begin,$end);
        $str = $this->parseLoop($str,$begin,$end);
        $str = $this->parseEndloop($str,$begin,$end);
        $str = $this->parseFunction($str,$begin,$end);
        $str = $this->parseTernary($str,$begin,$end);
        $str = $this->parseEmpty($str,$begin,$end);
        
        
        $str = $this->parseVariable($str,$begin,$end);
        $str = $this->parseProperty($str,$begin,$end);
        $str = $this->parse_addquote($str,$begin,$end);
        $str = $this->parse_Tag($str,$begin,$end);
        
        return $str;
    }
    
    /**
     * 解析三元条件表达式并替换为PHP代码
     *
     * @param string $str 需要解析的字符串
     * @param string $begin 表达式开始标记
     * @param string $end 表达式结束标记
     * @return string 替换后的PHP代码
     */
    private function parseTernary($str, $begin, $end) {
        // 正则表达式匹配 {$variable? 'value_if_true' : 'value_if_false'}
        return preg_replace(
            "/".$begin."\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*)\?([^{}]*)[:]?([^{}]*)".$end."/", 
            "<?php echo \\$\\1 ? \\2 \\3; ?>", 
            $str
        );
    }
    
    /**
     * 解析模板中的 {empty variable} 和 {/empty} 标签。
     *
     * @param string $str       要解析的字符串。
     * @param string $begin     标签的开始部分。
     * @param string $end       标签的结束部分。
     * @return string           解析后的字符串。
     */
    private function parseEmpty($str, $begin, $end) {
        // 正则表达式匹配 {empty variable} 和 {/empty}
        return preg_replace(
            array(
                "/".$begin."empty\\s+name=\"([^\"]*)\"".$end."/", 
                "/".$begin."\/empty".$end."/"
            ), 
            array(
                "<?php if (empty(\\$\\1)) { ?>", 
                "<?php } ?>"
            ), 
            $str, -1, $count
        );
    }


    /**
     * 解析 include 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseInclude($str,$begin,$end) {
        return preg_replace("/".$begin."qh:include\s+(.+)".$end."/", "<?php include view(\\1); ?>", $str);
    }
    
    /**
     * 解析 php 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parsePhp($str,$begin,$end) {
        return preg_replace("/".$begin."php\s+(.+)\s*".$end."/", "<?php \\1?>", $str);
    }
    
    /**
     * 解析 if 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseIf($str,$begin,$end) {
        return preg_replace("/".$begin."if\s+(.+?)".$end."/", "<?php if(\\1) { ?>", $str);
    }
    
    /**
     * 解析 elseif 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseElseif($str,$begin,$end) {
        return preg_replace("/".$begin."elseif\s+(.+?)".$end."/", "<?php } elseif (\\1) { ?>", $str);
    }
    
    /**
     * 解析 else 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseElse($str,$begin,$end) {
        return preg_replace("/".$begin."else".$end."/", "<?php } else { ?>", $str);
    }
    
    /**
     * 解析 endif 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseEndif($str,$begin,$end) {
        return preg_replace("/".$begin."\/if".$end."/", "<?php } ?>", $str);
    }
    
    /**
     * 解析 for 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseFor($str,$begin,$end) {
        return preg_replace("/".$begin."for\s+(.+?)".$end."/", "<?php for(\\1) { ?>", $str);
    }
    
    /**
     * 解析 endfor 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseEndfor($str,$begin,$end) {
        return preg_replace("/".$begin."\/for".$end."/", "<?php } ?>", $str);
    }
    
    /**
     * 解析 inc 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseInc($str,$begin,$end) {
        return preg_replace("/".$begin."\+\+(.+?)".$end."/", "<?php ++\\1; ?>", $str);
    }
    
    /**
     * 解析 dec 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseDec($str,$begin,$end) {
        return preg_replace("/".$begin."\-\-(.+?)".$end."/", "<?php --\\1; ?>", $str);
    }
    
    /**
     * 解析 loop 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseLoop($str,$begin,$end) {
        return preg_replace("/".$begin."loop\s+(\S+)\s+(\S+)".$end."/", "<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>", $str);
    }
    
    /**
     * 解析 endloop 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseEndloop($str,$begin,$end) {
        return preg_replace("/".$begin."\/loop".$end."/", "<?php } ?>", $str);
    }
    
    /**
     * 解析 function 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseFunction($str,$begin,$end) {
        return preg_replace("/".$begin."([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))".$end."/", "<?php echo \\1;?>", $str);
    }
    
    /**
     * 解析 variable 标签
     *
     * @param string $str 待解析的字符串
     * @param string $begin 标签开始符号
     * @param string $end 标签结束符号
     * @return string 解析后的字符串
     */
    private function parseVariable($str,$begin,$end) {
        return preg_replace("/".$begin."\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))".$end."/", "<?php echo \\1;?>", $str);
    }
    
    /**
     * 解析属性
     *
     * @param string $str 要解析的字符串
     * @param string $begin 属性开始标记
     * @param string $end 属性结束标记
     * @return string 解析后的字符串
     */
    private function parseProperty($str,$begin,$end) {
        return preg_replace("/".$begin."(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)->(\w+)(.*?)".$end."/", "<?php echo \\1->\\2\\3;?>", $str);
    }
    
    /**
     * 为字符串添加引号
     *
     * @param string $str 要处理的字符串
     * @param string $begin 引号开始标记
     * @param string $end 引号结束标记
     * @return string 处理后的字符串
     */    
    private function parse_addquote($str,$begin,$end) {
        return preg_replace_callback("/".$begin."(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)".$end."/s",  array($this, 'addquote'), $str); 
    }
    
    /**
     * 解析标签
     *
     * @param string $str 要解析的字符串
     * @param string $begin 标签开始标记
     * @param string $end 标签结束标记
     * @return string 解析后的字符串
     */    
    private function parse_Tag($str,$begin,$end) {
        return preg_replace_callback("/".$begin."qh:(\w+)\s+([^}]+)".$end."/i", array($this, 'tag_callback'), $str);
    }

    /**
     * 处理标签回调函数
     *
     * @param array $matches 匹配到的标签信息
     * @return string 处理后的标签内容
     */
    public static function tag_callback($matches) {
        return self::tags($matches[1], $matches[2], $matches[0]);
    }

    /**
     * 处理标签
     *
     * @param string $action 标签动作
     * @param string $data 标签数据
     * @param string $html 标签 HTML 代码
     * @return string 处理后的标签内容
     */
    public static function tags($action, $data, $html) {
        preg_match_all("/([a-z]+)\=[\"]?([^\"]+)[\"]?/i", stripslashes($data), $matches, PREG_SET_ORDER);

        // 处理解析后的数据
        $attributes = [];
        foreach ($matches as $match) {
            $attributes[$match[1]] = $match[2];
        }

        $return = 'data';
        $content = '';
        $tag = C('taglib_pre_load');
        $tagsInstance = new $tag();

        $action = 'tag' . ucfirst($action);
        $content .= '$tag = unserialize(' . var_export(serialize($tagsInstance), true) . ');';
        $content .= 'if(method_exists($tag, \'' . $action . '\')) {';
        $content .= '$' . $return . ' = $tag->' . $action . '(' . var_export_to_html($attributes) . ');';
        if (isset($attributes['page'])) {
            $content .= '$pages = $tag->pages;';
        }
        $content .= '}';
        return '<?php ' . $content . '?>';
    }

    /**
     * 添加引号
     *
     * @param array $matches 匹配到的内容
     * @return string 处理后的内容
     */
    public function addquote($matches) {
        $var = '<?php echo ' . $matches[1] . ';?>';
        return str_replace("\\\"", "\"", preg_replace("/\\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\\]/s", "['\\1']", $var));
    }

}