<?php
// +----------------------------------------------------------------------
// | session助手函数
// +----------------------------------------------------------------------
use qhphp\session\Session;
use qhphp\session\File;

if (!function_exists('Session')) {
    function Session()
    {
        return Session::class;
    }
}
