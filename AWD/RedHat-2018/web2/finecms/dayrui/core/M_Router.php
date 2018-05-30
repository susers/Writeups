<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Router extends CI_Router {

    public function __construct()
    {
        $this->default_controller = 'Home';
        parent::__construct();
    }

    protected function _set_routing() {


        $_d = 's';
        $_d = isset($_GET[$_d]) ? trim($_GET[$_d], " \t\n\r\0\x0B/") : '';
        if ($_d !== '')
        {
            $this->uri->filter_uri($_d);
            $this->set_directory($_d);
        }

        $_c = trim($this->config->item('controller_trigger'));
        if ( ! empty($_GET[$_c]))
        {
            $this->uri->filter_uri($_GET[$_c]);
            $this->set_class($_GET[$_c]);

            $_f = trim($this->config->item('function_trigger'));
            if ( ! empty($_GET[$_f]))
            {
                $this->uri->filter_uri($_GET[$_f]);
                $this->set_method($_GET[$_f]);
            }

            $this->uri->rsegments = array(
                1 => $this->class,
                2 => $this->method
            );
        } else {

            /* FineCMS路由模式 ======= 开始 */
            $routes = array();
            // 加载路由配置文件
            include_once(APPPATH.'config/routes.php');

            /* FineCMS路由模式 */
            if (DR_URI) {
                $value = $mark = $uri = NULL;
                // 正则匹配路由规则
                foreach ($routes as $key => $val) {
                    if (@preg_match('/^'.$key.'$/U', DR_URI, $match)) {
                        unset($match[0]);
                        $uri = $val;
                        $value = $match;
                        break;
                    }
                }
                // 没有找到返回404
                if (!$uri) {
                    if (!is_file(WEBPATH.'cache/install.lock') && is_dir($_SERVER['DOCUMENT_ROOT'].'/'.DR_URI)) {
                        header('Content-Type: text/html; charset=utf8');
                        show_error('FineCMS禁止子目录安装，请放置在网站根目录安装', 404);
                    } else {
                        $this->set_class('api');
                        $this->set_method('s404');
                    }
                } else {
                    $i = 0;
                    // 设置默认控制器
                    $this->set_class(array_shift($array));
                    $this->set_method(array_shift($array));
                    // 组装GET参数
                    if ($array) {
                        foreach ($array as $k => $t) {
                            if ($i%2 == 0) {
                                $_GET[str_replace('$', '_', $t)] = isset($array[$k+1]) ? $array[$k+1] : '';
                            }
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
                return;
            }
            /* FineCMS路由模式 ======= 结束 */

            $this->_set_default_controller();
        }

        return;

    }
}