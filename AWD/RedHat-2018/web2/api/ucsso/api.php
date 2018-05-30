<?php
/**
 * UCSSO单点登录系统 (http://www.ucsso.com/)
 *
 * @version     1.0
 * @author      wangchunjie
 * @copyright   Copyright (c) 2013-2017 天睿程序设计 Inc. (http://www.ucsso.com/)
 */

define("IS_UCSSO", 1);

require 'config.php';
require 'helper.php';
$timestamp = time();
if($timestamp - $_GET['time'] > 3600) {
    !$_GET['action'] ? ucsso_jsonp(0, '授权超时') : exit('授权超时');
}

$input = ucsso_authcode(ucsso_safe_replace($_GET['code']), 'DECODE', UCSSO_APP_KEY);
if(!$input) {
    !$_GET['action'] ? ucsso_jsonp(0, '认证失败') : exit('通信认证失败');
}

parse_str($input, $param);
$param = ucsso_daddslashes($param, 1, TRUE);
if(!$param) {
    !$_GET['action'] ? ucsso_jsonp(0, '授权参数不存在') : exit('授权参数不存在');
}

switch ($_GET['action']) {


    case 'synlogout':
        // 同步退出
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        ucsso_setcookie('member_uid', 0, 0);
        ucsso_setcookie('member_cookie', 0, 0);
        break;

    case 'synlogin':
        // 同步登陆
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        if (isset($param['uid'])) {
            $config = require '../../config/system.php';
            ucsso_setcookie('member_uid', $param['uid'], 86400 * 365);
            ucsso_setcookie('member_cookie', substr(md5($config['SYS_KEY'].$param['password']), 5, 20), 86400 * 365);
        }

        break;


    default:
        ucsso_jsonp(1, '测试通信成功');
        exit;
}
exit;
