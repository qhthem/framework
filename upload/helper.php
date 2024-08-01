<?php
// +----------------------------------------------------------------------
// | 上传助手函数
// +----------------------------------------------------------------------
use qhphp\upload\Uploads;

/**
 * 获取 uploads 实例。
 *
 * @return uploads 返回 uploads 实例
 */
if (!function_exists('Uploads')) {
    function Uploads()
    {
        return new Uploads();
    }
}

/**
 * 将字节数转换为更易读的格式（如KB, MB, GB等）。
 * 
 * @param int $bytes 字节数。
 * @param int $precision 小数点后的位数，默认为2。
 * @return string 转换后的格式化字符串。
 * @author zhaosong
 */
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); // 单位数组
        $bytes = max($bytes, 0); // 确保字节数为非负数
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); // 计算幂次
        $pow = min($pow, count($units) - 1); // 确保幂次不超过单位数组长度
        $bytes /= pow(1024, $pow); // 将字节数转换为对应单位的数值
    
        return round($bytes, $precision) . ' ' . $units[$pow]; // 返回格式化后的字符串
    }
}

/**
 * 根据文件扩展名判断文件类型。
 * 
 * @param string $extension 文件扩展名。
 * @return string 返回文件类型，可能是'image'、'video'或'down'。
 * @author zhaosong
 */
if (!function_exists('extensions')) {
    function extensions($extension)
    {
        $image_extensions = [
            'jpg', 'jpeg', 'png', 'gif', 'bmp',
            'tiff', 'tif', 'webp', 'ico', 'svg',
            'psd', 'jpg2', 'jpx', 'cr2'
        ]; // 图片文件扩展名数组
        $video_extensions = [
            'mp4', 'avi', 'flv', 'mpg', 'mpeg',
            'wmv', 'mov', 'mkv', 'webm', 'ogv',
            '3gp', '3g2', 'rm', 'rmvb', 'vob',
            'm4v', 'm4a', 'mp3', 'f4v', 'f4a',
            'm2ts', 'ts', 'mts', 'divx', 'xvid'
        ]; // 视频文件扩展名数组
    
        if (in_array($extension, $image_extensions)) {
            // 如果扩展名在图片文件扩展名数组中，则返回'image'
            return "image";
        } elseif (in_array($extension, $video_extensions)) {
            // 如果扩展名在视频文件扩展名数组中，则返回'video'
            return "video";
        } else {
            // 否则，返回'down'
            return "down";
        }        
    }
}

/**
 * 根据文件扩展名返回对应的图标路径。
 * 如果指定的扩展名不存在于预定义的扩展名数组中，则返回默认的帮助图标。
 * 
 * @param string $ext 文件的扩展名。
 * @return string 返回对应扩展名的图标路径。
 * 
 * @author zhaosong
 */
if (!function_exists('file_icon')){
    function file_icon($ext){
        // 定义支持的扩展名数组
        $ext_arr = ['code','css','dir','doc','docx','gif','html','jpeg','jpg','js','mp3','mp4','pdf','php','png','ppt','pptx','psd','rar','sql','swf','txt','xls','xlsx','xml','zip'];
        
        // 检查传入的扩展名是否存在于数组中
        if(in_array($ext,$ext_arr)) {
            // 如果存在，返回对应扩展名的图标路径
            return 'static/images/ext/'.$ext.'.png';
        } else {
            // 如果不存在，返回默认的帮助图标路径
            return 'static/images/ext/hlp.png';
        }
    }
}