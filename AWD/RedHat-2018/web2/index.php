<?php
/**
 * https://gitee.com/greenlaw
 **/

header('Content-Type: text/html; charset=utf-8');

// 显示错误提示
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);
function_exists('ini_set') && ini_set('display_errors', TRUE);
function_exists('ini_set') && ini_set('memory_limit', '1024M');
function_exists('set_time_limit') && set_time_limit(100);

define('FCPATH', dirname(__FILE__).'/finecms/');
define('WEBPATH', dirname(__FILE__).'/');

!defined('SELF') && define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);

// 执行主程序
require FCPATH.'Init.php';