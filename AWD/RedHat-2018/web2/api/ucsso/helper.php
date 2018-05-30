<?php
/**
 * UCSSO单点登录系统 (http://www.ucsso.com/)
 *
 * @version     1.0
 * @author      wangchunjie
 * @copyright   Copyright (c) 2013-2017 天睿程序设计 Inc. (http://www.ucsso.com/)
 */

/**
 * 统一返回jsonp格式并退出程序
 */
function ucsso_jsonp($code, $msg, $data = array()){

    $callback = ucsso_safe_replace($_GET['callback']);
    !$callback && $callback = 'callback';

    echo $callback.'('.json_encode(ucsso_return_data($code, $msg, $data)).')';exit;
}

/**
 * 数据返回统一格式
 */
function ucsso_return_data($code, $msg = '', $data = array()) {
    return array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    );
}

/**
 * 安全过滤函数
 */
function ucsso_safe_replace($string, $diy = null) {

    $replace = array('%20', '%27', '%2527', '*', "'", '"', ';', '<', '>', "{", '}');
    $diy && is_array($diy) && $replace = ucsso_array2array($replace, $diy);
    $diy && !is_array($diy) && $replace[] = $diy;

    return str_replace($replace, '', $string);
}


/**
 * 将字符串转换为数组
 *
 * @param	string	$data	字符串
 * @return	array
 */
function ucsso_string2array($data) {

    if (is_array($data)) {
        return $data;
    } elseif (!$data) {
        return array();
    }

    $rt = json_decode($data, true);
    if ($rt) {
        return $rt;
    }

    return unserialize(stripslashes($data));
}

/**
 * 将数组转换为字符串
 *
 * @param	array	$data	数组
 * @return	string
 */
function ucsso_array2string($data) {
    return $data ? json_encode($data) : '';
}


function ucsso_daddslashes($string, $force = 0, $strip = FALSE) {
    if(!MAGIC_QUOTES_GPC || $force) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = ucsso_daddslashes($val, $force, $strip);
            }
        } else {
            $string = addslashes($strip ? stripslashes($string) : $string);
        }
    }
    return $string;
}

function ucsso_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

    $ckey_length = 4;

    $key = md5($key ? $key : UCSSO_APP_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}

function ucsso_setcookie($var, $value, $life = 0, $cookiedomain = '') {
    setcookie($var, $value,
        $life ? time() + $life : 0, '/',
        $cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}