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

function ucsso_stripslashes($string) {
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if(MAGIC_QUOTES_GPC) {
        return stripslashes($string);
    } else {
        return $string;
    }
}

function ucsso_api_input($data) {
    $s = urlencode(ucsso_authcode($data.'&agent='.md5($_SERVER['HTTP_USER_AGENT'])."&time=".time(), 'ENCODE', UCSSO_APP_KEY));
    return $s;
}


function ucsso_get_avatar($uid) {
    return UCSSO_API.'avatar.php?app='.UCSSO_APP_ID.'&uid='.$uid;
}

function ucsso_avatar($uid, $code) {
    return json_decode(ucsso_api_post('avatar', array(
        'uid' => $uid,
        'code' => $code,
    )), true);
}

function ucsso_api_requestdata($action, $arg='', $extra='') {
    $input = ucsso_api_input($arg);
    $post = "&action=$action&input=$input&appid=".UCSSO_APP_ID.$extra;
    return $post;
}


function ucsso_synlogin($uid) {
    return ucsso_api_post('synlogin', array('uid'=>intval($uid)));
}

function ucsso_synlogout() {
    return ucsso_api_post('synlogout', array('uid' => 0));
}

function ucsso_delete($uid) {
    return ucsso_json_decode(ucsso_api_post('delete', array(
        'uid' => $uid,
    )), true);
}

function ucsso_syncuid($id, $uid) {
    return ucsso_json_decode(ucsso_api_post('syncuid', array(
        'id' => (int)$id,
        'uid' => (int)$uid,
    )), true);
}

function ucsso_get_password($uid) {
    return ucsso_json_decode(ucsso_api_post('get_password', array(
        'uid' => (int)$uid,
    )), true);
}

function ucsso_register($username, $password, $email, $phone) {
    return ucsso_json_decode(ucsso_api_post('register', array(
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'phone' => $phone,
    )), true);
}

function ucsso_login($username, $password) {
    return json_decode(ucsso_api_post('login', array(
        'username' => $username,
        'password' => $password,
    )), true);
}

function ucsso_edit_password($uid, $password) {
    return ucsso_json_decode(ucsso_api_post('edit_password', array(
        'uid' => $uid,
        'password' => $password,
    )), true);
}

function ucsso_edit_email($uid, $email) {
    return ucsso_json_decode(ucsso_api_post('edit_email', array(
        'uid' => $uid,
        'email' => $email,
    )), true);
}

function ucsso_edit_phone($uid, $phone) {
    return ucsso_json_decode(ucsso_api_post('edit_phone', array(
        'uid' => $uid,
        'phone' => $phone,
    )), true);
}

function ucsso_json_decode($string, $a = false) {
    $rt = json_decode($string, true);
    return $rt ?  $rt : array(
        'code' => -404,
        'msg' => '服务端网络连接失败',
    );

}

function ucsso_api_post($action, $arg = array()) {
    $s = $sep = '';
    foreach($arg as $k => $v) {
        $k = urlencode($k);
        if(is_array($v)) {
            $s2 = $sep2 = '';
            foreach($v as $k2 => $v2) {
                $k2 = urlencode($k2);
                $s2 .= "$sep2{$k}[$k2]=".urlencode(ucsso_stripslashes($v2));
                $sep2 = '&';
            }
            $s .= $sep.$s2;
        } else {
            $s .= "$sep$k=".urlencode(ucsso_stripslashes($v));
        }
        $sep = '&';
    }
    $postdata = ucsso_api_requestdata($action, $s);
    return ucsso_fopen2(UCSSO_API.'api.php', 500000, $postdata, '', TRUE, UCSSO_APP_IP, 20);
}

function ucsso_fopen2($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
    $__times__ = isset($_GET['__times__']) ? intval($_GET['__times__']) + 1 : 1;
    if($__times__ > 2) {
        return '';
    }
    $url .= (strpos($url, '?') === FALSE ? '?' : '&')."__times__=$__times__";
    return ucsso_fopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block);
}

function ucsso_fopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
    $return = '';
    $matches = parse_url($url);
    !isset($matches['host']) && $matches['host'] = '';
    !isset($matches['path']) && $matches['path'] = '';
    !isset($matches['query']) && $matches['query'] = '';
    !isset($matches['port']) && $matches['port'] = '';
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;
    if($post) {
        $out = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: '.strlen($post)."\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }

    $fp = false;
    if(function_exists('fsockopen')) {
        $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    }
    if (!$fp && function_exists('pfsockopen')) {
        $fp = @pfsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    }
    if (!$fp && function_exists('curl_init')) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_POST           => true,
            CURLOPT_USERAGENT      => $_SERVER[HTTP_USER_AGENT],
            CURLOPT_POSTFIELDS     => $post,
        );
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    if(!$fp) {
        file_put_contents(dirname(__FILE__)."/error.txt", date('Y-m-d H:i:s').' 通信请求获取失败：'.$post);
        return '';
    } else {
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if(!$status['timed_out']) {
            while (!feof($fp)) {
                if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                    break;
                }
            }

            $stop = false;
            while(!feof($fp) && !$stop) {
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $return .= $data;
                if($limit) {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }
        @fclose($fp);
        return $return;
    }
}
