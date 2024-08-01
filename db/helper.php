<?php
// +----------------------------------------------------------------------
// | 数据库操作类助手函数
// +----------------------------------------------------------------------
use qhphp\db\Db;
if (!function_exists('db')){
    /**
     * 用于实例化一个数据表对象  如：db('admin');
     * @param $tabname	 表名称
     * @return object
     */	
    function db($tabname){
    	
    	$object = new Db();;
    	return $object->table($tabname);
    }
}
if (!function_exists('removeLastTenCharacters')) {

    /**
     * 移除字符串末尾的指定数量的字符。
     *
     * @param string $str 输入的字符串
     * @param int $num 要移除的字符数量，默认为 10
     * @return string 移除末尾指定数量字符后的字符串
     */
    function removeLastTenCharacters($str, $num = 10)
    {
        $totalLength = strlen($str);
        if ($totalLength > $num) {
            $max = max(0, $totalLength - $num);
            $str = substr($str, 0, -$max);
        }
        return $str;
    }
}

if (!function_exists('str_cut')){
    /**
     * 字符截取
     * @param $string 要截取的字符串
     * @param $length 截取长度
     * @param $dot	  截取之后用什么表示
     * @param $code	  编码格式，支持UTF8/GBK
     */
    function str_cut($string, $length, $dot = '...', $code = 'utf-8') {
    	$strlen = strlen($string);
    	if($strlen <= $length) return $string;
    	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    	$strcut = '';
    	if($code == 'utf-8') {
    		$length = intval($length-strlen($dot)-$length/3);
    		$n = $tn = $noc = 0;
    		while($n < strlen($string)) {
    			$t = ord($string[$n]);
    			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
    				$tn = 1; $n++; $noc++;
    			} elseif(194 <= $t && $t <= 223) {
    				$tn = 2; $n += 2; $noc += 2;
    			} elseif(224 <= $t && $t <= 239) {
    				$tn = 3; $n += 3; $noc += 2;
    			} elseif(240 <= $t && $t <= 247) {
    				$tn = 4; $n += 4; $noc += 2;
    			} elseif(248 <= $t && $t <= 251) {
    				$tn = 5; $n += 5; $noc += 2;
    			} elseif($t == 252 || $t == 253) {
    				$tn = 6; $n += 6; $noc += 2;
    			} else {
    				$n++;
    			}
    			if($noc >= $length) {
    				break;
    			}
    		}
    		if($noc > $length) {
    			$n -= $tn;
    		}
    		$strcut = substr($string, 0, $n);
    		$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    	} else {
    		$dotlen = strlen($dot);
    		$maxi = $length - $dotlen - 1;
    		$current_str = '';
    		$search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
    		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
    		$search_flip = array_flip($search_arr);
    		for ($i = 0; $i < $maxi; $i++) {
    			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
    			if (in_array($current_str, $search_arr)) {
    				$key = $search_flip[$current_str];
    				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
    			}
    			$strcut .= $current_str;
    		}
    	}
    	return $strcut.$dot;
    }
}