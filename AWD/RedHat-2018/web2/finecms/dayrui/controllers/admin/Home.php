<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Home extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->output->enable_profiler(FALSE);
    }

    /**
     * 重置
     */
    public function home() {
        $this->index();
    }

    /**
     * 首页
     */
    public function index() {

        $top = array();
        $smenu = $this->_get_menu();
        $topid = 0; // 顶部菜单id
        $top_menu = array(); // 生成的菜单
        foreach ($smenu as $ii => $t) {
            //$string.= '<li class="heading"><h3 class="uppercase" id="D_T_'.$ii.'">'.$t['top']['name'].'</h3></li>';
            $_link = 0; // 是否第一个链接菜单，0表示第一个
            $_left = 0; // 是否第一个分组菜单，0表示第一个
            $string = '';
            foreach ($t['data'] as $left) {
                $string.= '<li id="D_M_'.$left['left']['id'].'" class="dr_left nav-item '.($_left ? '' : 'active open').'">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="'.$left['left']['icon'].'"></i>
                        <span class="title">'.fc_lang($left['left']['name']).'</span>
                        <span class="arrow '.($_left ? '' : 'open').'"></span>
                    </a>';
                $string.= ' <ul class="sub-menu">';
                $_left = 1; // 标识以后的菜单就不是第一个了
                foreach ($left['data'] as $link) {
                    if (!$_link) {
                        // 第一个链接菜单时 指定class
                        $class = 'dr_link nav-item active open';
                        $t['top']['link'] = $link;
                    } else {
                        $class = 'dr_link nav-item';
                    }
                    $_link = 1; // 标识以后的菜单就不是第一个了
                    $link['icon'] = $link['icon'] ? $link['icon'] : 'icon-th-large';
                    $string.= '<li tid="'.$ii.'" fid="'.$left['left']['id'].'" id="_MP_'.$link['id'].'" class="'.$class.'"><a href="javascript:_MP(\''.$link['id'].'\', \''.$link['url'].'\');" ><i class="iconm '.$link['icon'].'"></i> <span class="title">'.fc_lang($link['name']).'</span></a></li>';
                }

                $string.= '</ul>';
                $string.= '</li>';
                $top_menu[$ii] = $string;
            }
            unset($t['top']['left']);
            $top[$topid] = $t['top'];
            $topid ++;
        }

        $mysite = array();
        foreach ($this->site_info as $sid => $t) {
            $mysite[$sid] = $t['SITE_NAME'];
        }

        ob_start();
        ob_clean();
        $this->template->assign(array(
            'mysite' => $mysite,
            'top' => $top,
            'left' => $top_menu,
        ));

        $this->template->display('index.html');
    }

    /**
     * 菜单缓存格式化
     */
    private function _get_menu() {

        $menu = require WEBPATH.'config/admin_menu.php';
        $smenu = array();
        foreach ($menu as $i => $t) {
            $left = array();
            $t['id'] = $i;
            $t['pid'] = 0;
            $t['tid'] = 0;
            foreach ($t['menu'] as $j => $m) {
                $m['id'] = $i.'-'.$j;
                $m['pid'] = $i;
                $m['tid'] = $i;
                $link = array();
                if ($m['mark'] == 'content-content') {
                    // 表单模型
                    $form = $this->dcache->get('form-'.SITE_ID);
                    if ($form) {
                        $mmm = array();
                        foreach ($form as $mm) {
                            $mmm[] = array(
                                'name' => $mm['name'].'表单',
                                'uri' => 'fcontent/index/mid/'.$mm['table'],
                                'icon' => 'fa fa-circle-o',
                            );
                        }
                        foreach ($m['menu'] as  $n) {
                            $mmm[] = $n;
                        }
                        $m['menu'] = $mmm;
                    }
                    // 表示内容模型的
                    $module = $this->dcache->get('module');
                    if ($module) {
                        $mmm = array();
                        foreach ($module as $mm) {
                            $mmm[] = array(
                                'name' => $mm['name'].'管理',
                                'uri' => 'content/index/mid/'.$mm['dirname'],
                                'icon' => 'fa fa-circle-o',
                            );
                        }
                        foreach ($m['menu'] as $n) {
                            $mmm[] = $n;
                        }
                        $m['menu'] = $mmm;
                    }
                } elseif ($m['mark'] == 'app') {
                    $local = $this->local_app();
                    if ($local) {
                        foreach ($local as $tt) {
                            if ($tt['menu']) {
                                foreach ($tt['menu'] as $curi => $cv) {
                                    $m['menu'][] = array(
                                        'name' => $cv[0],
                                        'uri' => $curi,
                                        'icon' => $cv[1],
                                    );
                                }
                            }
                        }
                    }
                }
                if (is_array($m['menu'])) {
                    foreach ($m['menu'] as $k => $n) {
                        $n['id'] = $i.'-'.$j.'-'.$k;
                        $n['pid'] = $i.'-'.$j;
                        $n['tid'] = $i;
                        if ($n['uri']) {
                            $n['url'] = $this->duri->uri2url($n['uri']);
                        }

                        $link[] = $n;
                    }
                }
                if ($link) {
                    $left[] = array('left' => $m, 'data' => $link);
                }

            }
            if ($left) {
                $smenu[$i] = array('top' => $t, 'data' => $left);
            }
        }

        return $smenu;
    }


    /**
     * 后台首页
     */
    public function main() {

        if (is_file(WEBPATH.'cache/install.new')) {
            @unlink(WEBPATH.'cache/install.new');
            $this->cache();
            return;
        }

        $server = @explode(' ', strtolower($_SERVER['SERVER_SOFTWARE']));
        if (isset($server[0]) && $server[0]) {
            $server = dr_strcut($server[0], 15);
        } else {
            $server = 'web';
        }

        $this->template->assign(array(
            'sip' => $this->_get_server_ip(),
            'mymain' => 1,
            'server' => ucfirst($server),
            'sqlversion' => $this->db->version(),
        ));
        $this->template->display('main.html');
    }

    /**
     * 更新全站缓存
     */
    public function cache() {

        $this->system_log('更新全站缓存');

        $this->template->assign(array(
            'app' => array(),
            'list' => $this->_cache_url(),
        ));
        $this->template->display('cache.html');

    }

    // 清除缓存数据
    public function clear() {
        if (IS_AJAX || $this->input->get('todo')) {
            $this->_clear_data();
            if (!IS_AJAX) {
                $this->admin_msg(fc_lang('全站缓存数据更新成功'), '', 1);
            }
        } else {
            $this->admin_msg('Clear ... ', dr_url('home/clear', array('todo' => 1)), 2);
        }
    }


    // 更新表结构
    public function dbcache() {
        if (IS_AJAX || $this->input->get('todo')) {
            $this->dcache->delete('table');
            $this->system_model->cache();
            if (!IS_AJAX) {
                $this->admin_msg(fc_lang('数据表结构缓存更新成功'), '', 1);
            }
        } else {
            $this->admin_msg('Clear ... ', dr_url('home/dbcache', array('todo' => 1)), 2);
        }
    }

    // 域名检查
    public function domain() {
        $ip = $this->_get_server_ip();
        $domain = $this->input->get('domain');
        if (gethostbyname($domain) != $ip) {
            exit(fc_lang('请将域名【%s】解析到【%s】', $domain, $ip));
        }
        exit('');
    }

    // 清除缓存数据
    private function _clear_data() {

        // 删除全部缓存文件
        $this->load->helper('file');
        delete_files(WEBPATH.'cache/sql/');
        delete_files(WEBPATH.'cache/file/');
        delete_files(WEBPATH.'cache/page/');
        delete_files(WEBPATH.'cache/index/');
        delete_files(WEBPATH.'cache/templates/');


        // 重置Zend OPcache
        function_exists('opcache_reset') && opcache_reset();

    }

    function mbonline() {

        $this->template->assign(array(
            'url' => 'http://api.poscms.net/shop/search-template-v-1-cxbb-1-iscms-1.html',
        ));
        $this->template->display('online.html');
    }

    function cjonline() {

        $this->template->assign(array(
            'url' => 'http://api.poscms.net/shop/search-app-v-1-cxbb-1-iscms-1.html',
        ));
        $this->template->display('online.html');
    }

    function helponline() {
        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                '帮助手册' => array('admin/home/helponline', 'book'),
            )),
        ));
        $this->template->display('help.html');
    }

}