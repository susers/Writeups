<?php
/**
 * https://gitee.com/greenlaw
 **/

define('EXT', '.php'); // PHP文件扩展名
define('SYSDIR', 'system'); // “系统文件夹”的名称
define('BASEPATH', FCPATH . 'system/'); // CI框架目录
define('VIEWPATH', FCPATH . 'dayrui/'); // 定义视图目录，我们把它当做主项目目录

require FCPATH.'dayrui/config/user_agents.php';

// 客户端判定
$host = strtolower($_SERVER['HTTP_HOST']);
$is_mobile = 0;
if ($mobiles) {
    foreach ($mobiles as $key => $val) {
        if (FALSE !== (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), $key))) {
            // 表示移动端
            $is_mobile = 1;
            break;
        }
    }
}

define('IS_PC', !$is_mobile); // 是否pc端
define('DOMAIN_NAME', $host); // 当前域名

// 当前URL
$pageURL = 'http';
isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && $pageURL.= 's';
$pageURL.= '://';
if (strpos($_SERVER['HTTP_HOST'], ':') !== FALSE) {
    $url = explode(':', $_SERVER['HTTP_HOST']);
    $url[0] ? $pageURL.= $_SERVER['HTTP_HOST'] : $pageURL.= $url[0];
} else {
    $pageURL.= $_SERVER['HTTP_HOST'];
}
$pageURL.= $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
define('FC_NOW_URL', $pageURL);

// 伪静态字符串
$uu = isset($_SERVER['HTTP_X_REWRITE_URL']) || trim($_SERVER['REQUEST_URI'], '/') == SELF ? trim($_SERVER['HTTP_X_REWRITE_URL'], '/') : ($_SERVER['REQUEST_URI'] ? trim($_SERVER['REQUEST_URI'], '/') : NULL);
$uri = strpos($uu, SELF) === 0 || strpos($uu, '?') === 0 ? '' : $uu; // 以index.php或者?开头的uri不做处理


// 根据路由来匹配S变量
if (!IS_ADMIN && $uri) {

    define('PAGE_CACHE_URL', ($is_mobile ? 'mobile-' : '').$host.'/'.ltrim($uri, '/'));
    // 加载单页缓存
    is_file(WEBPATH.'cache/page/'.md5(PAGE_CACHE_URL).'.html') && exit(file_get_contents(WEBPATH.'cache/page/'.md5(PAGE_CACHE_URL).'.html'));

    define('DR_URI', $uri);
    include FCPATH.'dayrui/config/routes.php';
    $rewrite = require WEBPATH.'config/rewrite.php';
    $routes = $rewrite && is_array($rewrite) && count($rewrite) > 0 ? array_merge($routes, $rewrite) : $routes;

    // 正则匹配路由规则
    $value = $u = '';
    foreach ($routes as $key => $val) {
        $match = array();
        if ($key == $uri || @preg_match('/^'.$key.'$/U', $uri, $match)) {
            unset($match[0]);
            $u = $val;
            $value = $match;
            break;
        }

    }
    if ($u) {
        if (strpos($u, 'index.php?') === 0) {
            // URL参数模式
            $_GET = array();
            $queryParts = explode('&', str_replace('index.php?', '', $u));
            foreach ($queryParts as $param) {
                $item = explode('=', $param);
                $_GET[$item[0]] = $item[1];
                if (strpos($item[1], '$') !== FALSE) {
                    $id = (int)substr($item[1], 1);
                    $_GET[$item[0]] = isset($match[$id]) ? $match[$id] : $item[1];
                }
            }
            !$_GET['c'] && $_GET['c'] = 'home';
            !$_GET['m'] && $_GET['m'] = 'index';
        } elseif (strpos($u, '/') !== false) {
            // URI分段模式
            $array = explode('/', $u);
            $s = array_shift($array);
            if (is_dir(FCPATH.'module/'.$s) || is_dir(FCPATH.'app/'.$s)) {
                $_GET['s'] = $s;
                $_GET['c'] = array_shift($array);
                $_GET['m'] = array_shift($array);
            } elseif (is_file(FCPATH.'dayrui/controllers/'.ucfirst($s).'.php')) {
                $_GET['c'] = $s;
                $_GET['m'] = array_shift($array);
            }
            // 组装GET参数
            if ($array) {
                foreach ($array as $k => $t) {
                    $i%2 == 0 && $_GET[str_replace('$', '_', $t)] = isset($array[$k+1]) ? $array[$k+1] : '';
                    $i ++;
                }
                if ($value) {
                    foreach ($_GET as $k => $v) {
                        if (strpos($v, '$') !== FALSE) {
                            $id = (int)substr($v, 1);
                            $_GET[$k] = isset($value[$id]) ? $value[$id] : $v;
                        }
                    }
                }
            }
        }
    } elseif (isset($_GET['s']) && !isset($_GET['c'])) {
        // 只存在唯一一个s参数时给他强制指向home控制器
        $_GET['c'] = 'home';
    }
}

define('APP_DIR', '');
define('APPPATH', FCPATH . 'dayrui/');

if (IS_ADMIN) {
    // 后台
    $_GET['s'] = 'admin';
    !defined('IS_MEMBER') && define('IS_MEMBER', FALSE);
} elseif (isset($_GET['s']) && $_GET['s'] == 'member') {
    // 会员
    define('IS_MEMBER', TRUE);
} else {
    // 前台
    $_GET['s'] = '';
    !defined('IS_MEMBER') && define('IS_MEMBER', FALSE);
}

$version = require WEBPATH.'config/version.php';

if(isset($_COOKIE['FINECMS_CONFIG'])){
    $config = $_COOKIE['FINECMS_CONFIG'];
    require FCPATH.'dayrui/config/config.class.php';
}


define('DR_UPDATE', $version['DR_UPDATE']);
define('DR_VERSION', $version['DR_VERSION']);

// 请求URI字符串
!defined('DR_URI') && define('DR_URI', '');

// 判断是否存在自定义程序
if (is_file(FCPATH.'My.php')) {
    require FCPATH.'My.php';
}

require BASEPATH . 'core/CodeIgniter.php'; // CI框架核心文件