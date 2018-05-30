<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class M_Controller extends CI_Controller {

    public $site; // 站点数据库对象
    public $site_info; // 站点信息数组
    public $branch; // 分支系统信息
    public $admin; // 管理员信息
    public $member; // 当前登录会员信息
    public $pagesize; // 会员中心分页数量
    public $markrule; // 会员权限规则标识
    public $member_rule; // 会员权限规则
    public $module_rule; // 会员模型权限规则
    public $mobile; // 是否手机端
    public $uid; // 当前登录的uid
    public $my_city; // 当前的定位城市
    public $my_position; // 当前的定位坐标
    public $module;

    // 临时变量
    public $post;
    public $data;
    public $dir;
    public $mark;
    public $replace_lang = 1;
    private $_auth;
    private $_link;
    private $_temp;
    private $_fanyi;

    /**
     * 构造函数
     */

    public function __construct() {
        parent::__construct();
        // 初始化环境和数据库
        $this->replace_lang = 1;
        $this->load->database();
        $this->load->library('user_agent');

        define('IS_AJAX', $this->input->is_ajax_request());
        define('IS_POST', $_SERVER['REQUEST_METHOD'] == 'POST' && count($_POST) ? TRUE : FALSE);
        define('SYS_TIME', $_SERVER['REQUEST_TIME'] ? $_SERVER['REQUEST_TIME'] : time());
        define('SITE_PATH', '/');
        define('THEME_PATH', '/statics/');

        // 全局化站点变量
        $config1 = require WEBPATH.'config/system.php'; // 加载网站系统配置文件
        !$config1['SYS_THUMB_DIR'] && $config1['SYS_THUMB_DIR'] = 'uploadfile/thumb';

        // https模式
        if (isset($config1['SYS_HTTPS'])) {
            define('SYS_HTTPS', intval($config1['SYS_HTTPS']));
        } else {
            define('SYS_HTTPS', 0);
        }

        $this->load->library('session');
        $domain = require WEBPATH.'config/domain.php'; // 加载站点域名配置文件
        $sitecfg = directory_map(WEBPATH.'config/site/'); // 加载全部站点的配置文件
        foreach ($sitecfg as $file) {
            $id = (int)basename($file);
            if (is_file(WEBPATH.'config/site/'.$file)) {
                $this->site_info[$id] = require WEBPATH.'config/site/'.$file;
                $this->site_info[$id]['SITE_ID'] = (int) $id;
                $this->site_info[$id]['SITE_PC'] = $this->site_info[$id]['SITE_URL'] = dr_http_prefix(($this->site_info[$id]['SITE_DOMAIN'] ? $this->site_info[$id]['SITE_DOMAIN'] : DOMAIN_NAME).SITE_PATH);
                $this->site_info[$id]['SITE_MURL'] = dr_http_prefix(($this->site_info[$id]['SITE_MOBILE'] ? $this->site_info[$id]['SITE_MOBILE'] : DOMAIN_NAME).SITE_PATH);
                $this->site[$id] = $this->db; // 站点数据库对象
            }
        }
        unset($sitecfg);

        // 分析站点信息
        $siteid = (IS_ADMIN || $this->router->method == 'html') && $this->session->userdata('siteid') ? (int)$this->session->userdata('siteid') : 0;
        if ($siteid && isset($this->site_info[$siteid])) {
            // && isset($domain[DOMAIN_NAME])
            // 通过session来获取siteid
            define('SITE_ID', $siteid);
        } elseif (isset($domain[DOMAIN_NAME]) && isset($this->site_info[$domain[DOMAIN_NAME]])) {
            // 通过域名来获取siteid
            $siteid = (int)$domain[DOMAIN_NAME];
            $orthers = @explode(',', $this->site_info[$siteid]['SITE_DOMAINS']);
            $this->site_info[$siteid]['SITE_M_URL'] = $this->site_info[$siteid]['SITE_PC'] = $this->site_info[$siteid]['SITE_URL']; // PC端为主域名
            $uri = isset($_SERVER['HTTP_X_REWRITE_URL']) && trim($_SERVER['REQUEST_URI'], '/') == SELF ? trim($_SERVER['HTTP_X_REWRITE_URL'], '/') : ($_SERVER['REQUEST_URI'] ? trim($_SERVER['REQUEST_URI'], '/') : '');
            if ($orthers && DOMAIN_NAME != $this->site_info[$siteid]['SITE_DOMAIN'] && in_array(DOMAIN_NAME, $orthers)) {
                // 判断当前域名为“其他域名”
                // 301 转向开启
                defined('SITE_URL_301') && SITE_URL_301 && redirect(dr_http_prefix($this->site_info[$siteid]['SITE_DOMAIN'].'/'.$uri), '', '301');
                $this->site_info[$siteid]['SITE_PC'] = $this->site_info[$siteid]['SITE_URL'] = dr_http_prefix(DOMAIN_NAME.'/');

            } elseif (isset($this->site_info[$siteid]['SITE_MOBILE']) && $this->site_info[$siteid]['SITE_MOBILE']) {
                // 当前网站存在移动端域名时
                if (DOMAIN_NAME == $this->site_info[$siteid]['SITE_MOBILE']) {
                    // 当此域名是移动端域名时重新赋值给主站URL
                    $this->site_info[$siteid]['SITE_URL'] = dr_http_prefix(DOMAIN_NAME.'/');
                    $this->site_info[$siteid]['SITE_MOBILE'] = TRUE;
                } elseif ($this->_is_mobile()
                    && $this->site_info[$siteid]['SITE_MOBILE_OPEN']
                    && $this->site_info[$siteid]['SITE_MOBILE']
                    && DOMAIN_NAME == $this->site_info[$siteid]['SITE_DOMAIN']
                    && !IS_ADMIN) {
                    // 当网站开启强制定向时，并且存在移动端域名、URL是主站的域名、非后台
                    redirect(dr_http_prefix($this->site_info[$siteid]['SITE_MOBILE'].'/'.$uri), '', '301');
                    exit;
                }
            } elseif ($this->agent->is_mobile() && $this->site_info[$siteid]['SITE_MOBILE_OPEN']) {
                // 识别移动端
                $this->site_info[$siteid]['SITE_MOBILE'] = TRUE;
            }
            define('SITE_ID', $siteid);
        } else {
            // 识别移动端
            if ($this->agent->is_mobile() && $this->site_info[1]['SITE_MOBILE_OPEN']) {
                $this->site_info[1]['SITE_PC'] = $this->site_info[1]['SITE_URL'];
                $this->site_info[1]['SITE_MOBILE'] = TRUE;
            }
            // 默认站点id
            define('SITE_ID', 1);
        }

        $config3 = array();
        $this->site_info[SITE_ID] && $config3 = array_merge($config1, $this->site_info[SITE_ID]);

        define('ADMIN_URL', dr_http_prefix(DOMAIN_NAME.'/'));

        // 默认文件上传目录
        $config3['SYS_UPLOAD_DIR'] = 'uploadfile';
        $config3['SYS_UPLOAD_PATH'] = WEBPATH.$config3['SYS_UPLOAD_DIR'];
        $config3['SYS_ATTACHMENT_URL'] = $config3['SITE_URL'].$config3['SYS_UPLOAD_DIR'];
        $config3['SYS_ATTACHMENT_URL'] = trim($config3['SYS_ATTACHMENT_URL'], '/').'/';
        $config3['UEDITOR_IMG_ID'] = md5($config3['SYS_ATTACHMENT_URL']); // 编辑器附件ID


        // 将配置文件数组转换为常量
        foreach ($config3 as $var => $value) {
            if ($var == 'SITE_MOBILE' && $value === TRUE && IS_ADMIN) {
                $value = '';
            } elseif (($var == 'SITE_THEME' && $value =='SITE_THEME')
                || ($var == 'SITE_TEMPLATE' && $value =='SITE_TEMPLATE')) {
                $value = 'default';
            } elseif (strpos($value, 'SYS_CACHE') === 0) {
                $value = (int)$value;
            }
            !defined($var) && define($var, $value); // 定义站点常量
        }
        unset($config3, $config2, $config1);


        // 定义模板目录
        define('TPLPATH', WEBPATH.'templates/');

        // 定义pc端域名
        !defined('SITE_PC') && define('SITE_PC', SITE_URL);

        // 判断手机端与PC端模板
        $this->mobile = SITE_MOBILE === TRUE ? 1 : 0;

        // 是否移动端
        define('IS_MOBILE', $this->mobile);

        // 显示错误提示
        SYS_DEBUG && error_reporting(E_ALL ^ E_NOTICE);

        $this->config->set_item('language', SITE_LANGUAGE); // 网站语言
        date_default_timezone_set('Etc/GMT'.(SITE_TIMEZONE > 0 ? '-' : '+').abs(SITE_TIMEZONE)); // 设置时区

        $this->load->model('system_model');

        $this->load->model('member_model'); // 加载会员模型

        $MEMBER = $this->get_cache('member');

        if ($MEMBER['setting']['ucsso'] && is_file(WEBPATH.'api/ucsso/config.php')) {
            include WEBPATH.'api/ucsso/client.php';
        }

        // 获取当前的登录记录
        $this->uid or $this->uid = (int)$this->member_model->member_uid();
        $this->member = $this->member_model->get_member($this->uid);

        // 验证账号cookie的有效性
        if (!$this->member_model->check_member_login()) {
            $this->uid = 0;
            $this->member = array();
        }

        // 会员不存在时，uid设置为0
        !$this->member && $this->uid = 0;

        // 全部模型
        $this->module = $this->get_cache('module');

        define('MEMBER_URL', SITE_URL.'index.php?s=member');
        define('MEMBER_PATH', '/');

        // 网站风格地址
        !defined('HOME_THEME_PATH') && define('HOME_THEME_PATH', strpos(SITE_THEME, 'http') === 0 ? trim(SITE_THEME, '/').'/' : THEME_PATH.SITE_THEME.'/');

        $this->load->library('template');
        $this->template->mobile = $this->mobile;

        // 当网站处于关闭状态时，非管理员无法访问
        defined('SITE_CLOSE') && SITE_CLOSE && !IS_ADMIN
        && !$this->member['adminid']
        && $this->router->class != 'api'
        && !($this->router->class == 'home' && $this->router->method == 'captcha')
        && $this->msg(SITE_CLOSE_MSG);
        unset($url, $theme, $template);

        if (defined('IS_MEMBER') && IS_MEMBER) {
            // 会员部分
            $this->load->helper(array('system', 'url'));
            $uri = trim(str_replace('member/', '/', str_replace('/member/', '/', $this->duri->uri(NULL, TRUE))), '/');


            $this->template->assign(array(
                'uid' => $this->uid,
                'regverify' => $MEMBER['setting']['regverify'],
            ));

            // 登录判断
            if (!defined('DR_UEDITOR')
                && !defined('DR_PAY_ID')
                && !defined('DISCUZ_ROOT')
                && !in_array($this->router->class, array('register', 'login', 'api', 'sns'))) {
                $url = dr_member_url('login/index', array('backurl' => urlencode(dr_now_url())));
                // 没有登录时
                !$this->member && $this->member_msg(fc_lang('会话超时，请重新登录').$this->member_model->logout(), $url);
            }

            if (defined('DR_UEDITOR') && is_dir(WEBPATH.'api/'.DR_UEDITOR)) {
                require WEBPATH.'api/'.DR_UEDITOR.'/php/controller.php';
                exit;
            }

        } elseif (IS_ADMIN) {

            $this->load->helper(array('system', 'url'));
            $uri = $this->duri->uri();
            $this->admin = $this->is_admin_login();

            // 权限判断
            !$this->is_auth($uri)
            && (IS_AJAX ? exit(fc_lang('您无权限操作(%s)', $uri)) : $this->admin_msg(fc_lang('您无权限操作(%s)', $uri)));

            $this->template->assign('admin', $this->admin);

            // 后台性能分析
            SYS_DEBUG && !IS_AJAX && IS_PC && $this->router->class != 'api' && $this->output->enable_profiler(TRUE);

        }

        // 销毁变量
        unset($MOD, $MEMBER);

        $this->template->ci = $this;
        $this->template->assign(array(
            'get' => $this->input->get(NULL, TRUE),
            'member' => $this->member,
            'site_info' => $this->site_info,
            'is_mobile' => $this->template->mobile,
        ));


        $this->hooks->call_hook('finecms-init'); // 挂钩点
    }



    protected function _is_mobile() {

        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
            return true;
        } elseif (isset ($_SERVER['HTTP_USER_AGENT'])) {
            // 判断手机发送的客户端标志,兼容性有待提高
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

    public function clear_cache($name) {

        if (!$name) {
            return;
        }

        $name = strtolower($name);

        // 模型缓存时，清除所有相关文件
        if ($name == 'module') {
            $data = $this->get_cache('module');
            if ($data) {
                foreach ($data as $site => $t) {
                    if ($t) {
                        foreach ($t as $m) {
                            $tname = 'module-'.$site.'-'.$m;
                            SYS_MEMCACHE && $this->cache->memcached->is_supported() && $this->cache->memcached->delete(SYS_KEY.$tname);
                        }
                    }
                }
            }
        }

        // 删除文件缓存
        $this->cache->file->delete($name);

        return;
    }


    public function get_cache() {

        $param = func_get_args();
        if (!$param) {
            return NULL;
        }

        // 取第一个参数作为缓存变量名称
        $data = $model = NULL;
        $name = strtolower(array_shift($param));
        /*
        isset($param[0])
        && strpos($param[0], '_model') !== false
        && is_file(FCPATH.'dayrui/models/'.ucfirst($param[0]).'.php') && $model = strtolower(array_shift($param));
           */

        if (!$data) {
            $var = 'cache-'.$name;
            if (isset($this->$var) && $this->$var) {
                // 读取全局变量
                $data = $this->$var;
            } else {
                // 读取本地文件缓存数据
                $data = $this->$var = $this->dcache->get($name);
            }
        }

        if (!$param) {
            return $data;
        }

        $var = '';
        foreach ($param as $v) {
            $var.= '[\''.dr_safe_replace($v).'\']';
        }

        $return = NULL;
        @eval('$return = $data'.$var.';');

        return $return;
    }


    public function get_cache_data($name) {

        if (!$name) {
            return NULL;
        } elseif (defined('SYS_AUTO_CACHE') && !SYS_AUTO_CACHE) {
            // 禁用缓存
            return NULL;
        }


        return defined('SYS_MEMCACHE') && SYS_MEMCACHE && $this->cache->memcached->is_supported() ? $this->cache->memcached->get(SYS_KEY.$name) : @$this->cache->file->get($name);
    }


    public function set_cache_data($name, $data, $ttl = 10) {

        if (defined('SYS_AUTO_CACHE') && !SYS_AUTO_CACHE) {
            // 禁用缓存
            return $data;
        }

        $ttl = (int)$ttl;

        if (!$name || !$ttl) {
            return $data;
        }

        defined('SYS_MEMCACHE') && SYS_MEMCACHE && $this->cache->memcached->is_supported() ? $this->cache->memcached->save(SYS_KEY.$name, $data, $ttl) : $this->cache->file->save($name, $data, $ttl);

        return $data;
    }



    public function is_auth($uri) {

        return TRUE;
    }

    // 获取表字段结构
    public function get_table_field($table) {

        $field = $this->get_cache_data('field-'.$table);
        if (!$field) {
            $this->load->model('system_model');
            $tableinfo = $this->system_model->cache(); // 表结构缓存
            $field = $tableinfo[$table]['field'];
            $this->set_cache_data('field-'.$table, $field, 3600);
        }

        return $field;
    }

    // 获取自定义控制器
    public function get_sysc($id) {
        return $this->db->where('id', $id)->limit(1)->get('controller')->row_array();
    }

    // 服务器ip地址
    protected function _get_server_ip() {

        if (isset($_SERVER['SERVER_ADDR'])
            && $_SERVER['SERVER_ADDR']
            && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
            return $_SERVER['SERVER_ADDR'];
        }

        return gethostbyname($_SERVER['HTTP_HOST']);
    }

    /**
     * 后台操作界面中的顶部导航菜单
     *
     * @param	array	$menu
     * @return	string
     */
    protected function get_menu_v3($menu) {

        if (!$menu) {
            return NULL;
        }

        $_i = 1;
        $_cur = ''; // 表示当前菜单
        $_uri = $this->duri->uri(1); // 当前uri
        $_name = ''; // 表示当前菜单名称
        $_link = '';
        $_first = ''; // 第一个菜单的名称
        $_quick = array(); // 作为快捷菜单

        foreach ($menu as $name => $t) {
            $uri = $t[0];
            $uri = trim($uri, '/'); // 格式菜单uri
            if (!$name || !$uri) {
                continue;
            }
            // 获取URL
            if (substr($uri, (int)strpos($uri, '_js')) == '_js') {
                $uri = substr($uri, 0, -3);
                $url = dr_dialog_url($this->duri->uri2url($uri), 'add');
            } elseif (strpos($uri, 'javascript') === 0) {
                $url = $uri;
            } elseif (strpos($uri, SELF) !== FALSE) {
                $url = $uri;
            } else {
                $url = $this->duri->uri2url($uri);
            }
            // 验证URI权限
            if (!$this->is_auth($uri)) {
                continue;
            }
            // 判断选中的当前菜单
            if ($_i > 1 && $this->get_menu_calss($menu, $uri, $_uri)) {
                $_cur = $name;
                $class = ' class="blue"';
            } elseif ($_i == 1) {
                $class = ' class="{MARK}"';
            } else {
                $class = '';
            }
            $_first = $_i == 1 ? $name : $_first;
            // 快捷菜单
            $_quick[] = array(
                'url' => $url,
                'name' => $name,
                'icon' => '<i class="fa fa-'.$t[1].'"></i>',
            );
            // 生成链接
            $_link.= '<li> <a href="'.$url.'" '.$class.'>'.($t[1] ? '<i class="fa fa-'.$t[1].'"></i> ' : '').$name.'</a> <i class="fa fa-circle"></i> </li>';
            $_i ++;
        }

        // 修改菜单时
        if (!$_cur && $this->router->method == 'edit') {
            $_link.= '<li><a href="javascript:;" class="blue"><i class="fa fa-edit"></i> '.fc_lang('修改').'</a> <i class="fa fa-circle"></i> ';
            $_cur = fc_lang('修改');
        }

        if (!$_cur) {
            $_link = str_replace('{MARK}', 'blue', $_link);
            !$_name && $_name = $_first;
        } else {
            $_name = $_cur;
        }

        return array(
            'name' => $_name,
            'link' => $_link,
            'quick' => $_quick,
        );
    }

    /**
     * 后台操作界面中的顶部导航菜单
     *
     * @param	array	$menu
     * @return	string
     */
    protected function get_menu($menu) {

        if (!$menu) {
            return NULL;
        }

        $_i = 0;
        $_str = '';
        $_uri = $this->duri->uri(1); // 当前uri
        $_mark = TRUE;

        foreach ($menu as $name => $t) {
            if (is_array($t)) {
                $uri = $t[0];
                $name = '<i class="fa fa-'.$t[1].'"></i> '.$name;
            } else {
                $uri = $t;
            }
            $uri = trim($uri, '/');
            if (!$name || !$uri) {
                continue;
            }
            $class = '';
            if (strpos($uri, '_js') !== FALSE) {
                $uri = substr($uri, 0, -3);
                $url = dr_dialog_url($this->duri->uri2url($uri), 'add');
            } elseif (strpos($uri, 'javascript') === 0) {
                $url = $uri;
            } else {
                $url = $this->duri->uri2url($uri);
                $class = ' class="onloading '.(!$_i ? '{FIR}' : '').'"';
            }
            if (!$this->is_auth($uri)) {
                continue;
            }
            $mark = $_i == 0 ? '{MARK}' : '';
            // 判断选中
            if ($this->get_menu_calss($menu, $uri, $_uri)) {
                $_mark = FALSE;
                $class = ' class="onloading on"';
            }
            $class = strpos($url, 'target') ? '' : $class;
            $_str.= '<a href="'.$url.'" '.$class.$mark.'><em>'.$name.'</em></a><span>|</span>';
            $_i ++;
        }

        if ($_mark && $this->router->method == 'edit') {
            $_str.= '<a href="javascript:;" class="on"><em>'.fc_lang('修改').'</em></a><span>|</span>';
            $_mark = FALSE;
        }

        $_str = $_mark ? str_replace('{FIR}', 'on', $_str) : $_str;

        return $_mark ? str_replace('{MARK}', ' class="on"', $_str) : str_replace('{MARK}', '', $_str);
    }

    private function get_menu_calss($menu, $uri, $_uri) {

        if ($uri == $_uri) {
            return TRUE;
        }

        if (!in_array($_uri, $menu)) {
            if (@strpos($_uri, $uri) === FALSE) {
                return FALSE;
            }
            $uri_arr = explode('/', $_uri);
            $uri_arr = array_slice($uri_arr, 0, -2);
            $__uri = implode('/', $uri_arr);
            if (in_array($__uri, $menu) && $__uri == $uri) {
                return TRUE;
            }
            if ($this->get_menu_calss($menu, $uri, $__uri)) {
                return TRUE;
            }
            if (strpos($__uri, $uri) === 0) {
                return TRUE;
            }
        }

    }

    /**
     * 用于后台的分页
     */
    protected function get_pagination($url, $total) {

        $this->load->library('pagination');

        $config['base_url'] = $url;
        $config['per_page'] = SITE_ADMIN_PAGESIZE;
        $config['next_link'] = fc_lang('下一页');
        $config['prev_link'] = fc_lang('上一页');
        $config['last_link'] = fc_lang('最后一页');
        $config['first_link'] = fc_lang('第一页');
        $config['total_rows'] = $total;
        $config['cur_tag_open'] = '<span>';
        $config['cur_tag_close'] = '</span>';
        $config['use_page_numbers'] = TRUE;
        $config['query_string_segment'] = 'page';

        $this->pagination->initialize($config);

      
        return $this->pagination->create_links();
    }

    /**
     * 会员中心分页
     */
    public function get_member_pagination($url, $total) {

        $this->load->library('pagination');

        $config = array();
        is_file(FCPATH.'module/member/config/pagination.php') ? include FCPATH.'module/member/config/pagination.php' : include WEBPATH.'config/pagination.php';

        $page = $config['pagination'];
        $page['base_url'] = $url;
        $page['per_page'] = $this->pagesize;
        $page['total_rows'] = $total;
        $page['use_page_numbers'] = TRUE;
        $page['query_string_segment'] = 'page';

        $this->pagination->initialize($page);

        return $this->pagination->create_links();
    }

    /**
     * 后台登录判断，返回当前登录用户信息
     *
     * @return void
     */
    protected function is_admin_login() {

        if (IS_ADMIN && ($this->router->class == 'login' || $this->router->method == 'login')) {
            return FALSE;
        }

        $uid = (int) $this->session->userdata('uid');
        $admin = (int) $this->session->userdata('admin');
        if ($this->uid == FALSE
            || $uid != $this->uid
            || $admin != $uid) {
            if (IS_AJAX) {
                exit(fc_lang('登录超时'));
            } elseif (IS_ADMIN) {
                redirect(ADMIN_URL.dr_url('login/index', array('backurl' => urlencode(dr_now_url()))), 'refresh');
            }
            return FALSE;
        }

        $data = $this->member_model->get_admin_member($this->uid, 1);
        if (!is_array($data)) {
            if (IS_ADMIN) {
                if ($data == 0) {
                    IS_AJAX ? exit(dr_json(0, fc_lang('会员不存在'))) : $this->admin_msg(fc_lang('会员不存在'));
                } elseif ($data == -3) {
                    IS_AJAX ? exit(dr_json(0, fc_lang('您无权限登录管理平台'))) : $this->admin_msg(fc_lang('您无权限登录管理平台'));
                } elseif ($data == -4) {
                    IS_AJAX ? exit(dr_json(0, fc_lang('您无权限登录该站点'))) : $this->admin_msg(fc_lang('您无权限登录该站点'));
                }
            } else {
                return $data;
            }
        }

        return $data;
    }

    // 返回json格式
    public function callback_json($data) {

        // 自定义返回名称
        $return = $this->input->get('return', true);
        if (isset($data['error']) && !isset($data['msg'])) {
            $data['msg'] = $data['error'];
            unset($data['error']);
        }

        if ($return) {
            $temp = $data;
            $data = array();
            foreach ($temp as $i => $t) {
                $data[$i.'_'.$return] = $t;
            }
        }

        return json_encode($data);
    }

    /**
     * 后台提示消息显示
     *
     * @param	string	$msg	提示信息
     * @param	string	$url	转向地址
     * @param	int		$mark	标示符号1：成功；0：失败；2：等待
     * @param	int		$time	等待时间
     * @return  void
     */
    public function admin_msg($msg, $url = '', $mark = 0, $time = 1) {

        IS_AJAX && exit(dr_json(($mark ? 1 : 0), $msg, $url));
        $this->template->assign(array(
            'msg' => $msg,
            'url' => $this->security->xss_clean($url),
            'time' => $time,
            'mark' => $mark,
            'cache_url' => IS_ADMIN && IS_PC ? $this->_cache_url() : array(),
        ));
        $this->template->display('msg.html', 'admin');
        exit;
    }

    /**
     * 会员提示消息显示
     *
     * @param	string	$msg	提示信息
     * @param	string	$url	转向地址
     * @param	int		$mark	标示符号1：成功；0：失败；2：等待
     * @param	int		$time	等待时间
     * @param	bool	$ajax	是否ajax提交显示
     * @return  void
     */
    public function member_msg($msg, $url = '', $mark = 0, $time = 1, $ajax = FALSE) {

        // 当指定为ajax提交或者系统提交状态为ajax则返回json数据
        if ($ajax || IS_AJAX) {
            exit(dr_json(($mark ? 1 : 0), $msg, $url));
        } else {
            $this->template->assign(array(
                'msg' => $msg,
                'url' => $this->security->xss_clean($url),
                'time' => $time,
                'mark' => $mark,
                'meta_name' => fc_lang('提示信息')
            ));
            IS_MEMBER ? $this->template->display('msg.html', 'member') : $this->template->display('msg.html');
        }
        exit;
    }

    /**
     * 前端提示消息显示
     *
     * @param	string	$msg	提示信息
     * @param	string	$url	转向地址
     * @param	int		$mark	标示符号1：成功；0：失败；2：等待
     * @param	int		$time	等待时间
     * @return  void
     */
    public function msg($msg, $url = '', $mark = 0, $time = 1) {

        IS_AJAX && exit(dr_json(($mark ? 1 : 0), $msg, $url));
        $this->template->assign(array(
            'msg' => $msg,
            'url' => $this->security->xss_clean($url),
            'time' => $time,
            'mark' => $mark
        ));
        $this->template->display('msg.html', '/');

        exit;
    }

    /**
     * 迷你提示消息显示
     *
     * @param	string	$msg	提示信息
     * @param	string	$url	转向地址
     * @param	int		$mark	标示符号1：成功；0：失败；2：等待
     * @param	int		$time	等待时间
     * @return  void
     */
    public function mini_msg($msg, $url = '', $mark = 0, $time = 1) {
        $this->template->assign(array(
            'msg' => $msg,
            'url' => $this->security->xss_clean($url),
            'time' => $time,
            'mark' => $mark
        ));
        $this->template->display('mini_msg.html', 'admin');
        exit;
    }

    /**
     * 付款提示消息显示
     *
     * @param	string	$msg	提示信息
     * @param	string	$url	转向地址
     * @param	intval	$mark	标示符号1：成功；0：失败；2：等待
     * @return  void
     */
    protected function pay_msg($msg, $url = '', $mark = 0) {
        $this->template->assign(array(
            'url' => $this->security->xss_clean($url),
            'msg' => $msg,
            'mark' => $mark,
        ));
        $this->template->display('pay_msg.html');
        exit;
    }

    /**
     * 验证码验证
     *
     * @param	string	$id	验证码表单的name
     * @return  bool
     */
    protected function check_captcha($id) {

        if (isset($_POST['geetest_challenge'])
            && isset($_POST['geetest_validate'])) {
            require FCPATH.'dayrui/libraries/Geetestlib.php';
            $GtSdk = new GeetestLib();
            if ($this->session->userdata('gtserver') == 1) {
                $result = $GtSdk->validate(
                    $_POST['geetest_challenge'],
                    $_POST['geetest_validate'],
                    $_POST['geetest_seccode']
                );
                if ($result == TRUE) {
                    return TURE;
                } else if ($result == FALSE) {
                    return FALSE;
                } else {
                    return FALSE;
                }
            }else{
                if ($GtSdk->get_answer($_POST['geetest_validate'])) {
                    return TURE;
                }else{
                    return FALSE;
                }
            }
        } else {
            $data = $this->input->post($id);
            $data = $data ? $data : $this->input->get($id);
            if (!$data) {
                return FALSE;
            }
            $code = $this->session->userdata('captcha');
            if (strtolower($data) == $code) {
                $this->session->unset_userdata('captcha');
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * 验证码
     */
    public function captcha() {
        $this->load->library('captcha');
        $this->captcha->width = $this->input->get('width') ? $this->input->get('width') : 80;
        $this->captcha->height = $this->input->get('height') ? $this->input->get('height') : 30;
        $this->session->unset_userdata('captcha');
        $this->session->set_userdata('captcha', $this->captcha->get_code());
        $this->captcha->doimage();
    }

    /**
     * 获取系统运行信息
     *
     * @return  string
     */
    public function get_system_run_info() {
        return dr_lang(
            '页面执行时间%s秒，共%s个查询语句，消耗内存%s',
            $this->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end'),
            count($this->db->queries),
            str_replace(' ', '', dr_format_file_size(memory_get_usage() / 4))
        );
    }

    /**
     * 表单提交数据验证和过滤
     *
     * @param	array	$_field	字段
     * @param	array	$_data	修改前的数据
     * @return  array
     */
    protected function validate_filter($_field, $_data = array()) {

        if (!$_field) {
            return;
        }

        $this->_data = $_data;
        $this->syn_content = $this->post = $this->data = array();
        $this->load->library('Dfield');
        $this->load->library('Dfilter');
        $this->load->library('Dvalidate');

        foreach ($_field as $field) {
            // 前端字段筛选
            if (!IS_ADMIN && !$field['ismember']) {
                continue;
            }
            // 验证字段对象的有效性
            $obj = $this->dfield->get($field['fieldtype']);
            if (!$obj) {
                continue;
            }
            $name = $field['fieldname'];
            $validate = $field['setting']['validate'];
            // 禁止修改字段筛选
            if (IS_MEMBER && $validate['isedit'] && isset($_data[$name]) && $_data[$name]) {
                $this->post[$name] = $_data[$name];
                $obj->insert_value($field); // 获取入库值
                continue;
            }
            // 编辑器默认关闭xss
            $validate['xss'] = !isset($validate['xss']) && $field['fieldtype'] == 'Ueditor' ? 1 : $validate['xss'];
            // 从表单获取值
            $this->post[$name] = $value = $this->input->post("data[$name]", $validate['xss'] ? FALSE : TRUE);
            // 验证必填字段
            if ($field['fieldtype'] != 'Group'
                && isset($validate['required'])
                && $validate['required']) {
                // 验证值为空
                if ($value == '') {
                    return array('error' => $name, 'msg' => fc_lang('%s不能为空！', $field['name']));
                }
                // 当类别为联动时判定0值
                if ($field['fieldtype'] == 'Linkage' && !$value) {
                    return array('error' => $name, 'msg' => fc_lang('%s不能为空！', $field['name']));
                }
                // 正则验证
                if (isset($validate['pattern'])
                    && $validate['pattern']
                    && !preg_match($validate['pattern'], $value)) {
                    return array('error' => $name, 'msg' => $field['name'].'：'.($validate['errortips'] ? $validate['errortips'] : fc_lang('格式不正确')));
                }
            }
            // 函数/方法校验
            if (isset($validate['check']) && $validate['check']) {
                if (strpos($validate['check'], '_') === 0) {
                    // 方法格式：_方法名称[:现存数据字段,参数2...]
                    list($method, $_param) = explode(':', str_replace('::', ':', $validate['check']));
                    $method = substr($method, 1);
                    if (method_exists($this->dvalidate, $method)) {
                        $param['value'] = $value;
                        if ('check_member' == $method && $value == 'guest') {
                            // 游客不验证
                        } else {
                            if ($_param && $_value = explode(',', $_param)) {
                                foreach ($_value as $t) {
                                    $param[$t] = isset($_POST['data'][$t]) ? $this->input->post("data[$t]") : $t;
                                }
                            }
                            if (call_user_func_array(array($this->dvalidate, $method), $param)) {
                                return array('error' => $name, 'msg' => $field['name'].'：'.fc_lang('格式校验不正确'));
                            }
                        }
                    } else {
                        log_message('error', "校验方法 $method 不存在！");
                    }
                } else {
                    // 函数格式：函数名称[:现存数据字段,参数2...]
                    list($func, $_param) = explode(':', str_replace('::', ':', $validate['check']));
                    if (function_exists($func)) {
                        $param['value'] = $value;
                        if ($_param && $_value = explode(',', $_param)) {
                            foreach ($_value as $t) {
                                $param[$t] = isset($_POST['data'][$t]) ? $this->input->post("data[$t]") : $t;
                            }
                        }
                        if (call_user_func_array($func, $param)) {
                            return array('error' => $name, 'msg' => $field['name'].'：'.fc_lang('格式校验不正确'));
                        }
                    } else {
                        log_message('error', "校验函数 $func 不存在！");
                    }
                }
            }
            // 过滤函数
            if (isset($validate['filter']) && $validate['filter']) {
                if (strpos($validate['filter'], '_') === 0) {
                    // 方法格式：_方法名称[:现存数据字段,参数2...]
                    list($method, $_param) = explode(':', str_replace('::', ':', $validate['filter']));
                    $method = substr($method, 1);
                    if (method_exists($this->dfilter, $method)) {
                        $param['value'] = $value;
                        if ($_param && $_value = explode(',', $_param)) {
                            foreach ($_value as $t) {
                                $param[$t] = isset($_POST['data'][$t]) ? $this->input->post("data[$t]") : $t;
                            }
                        }
                        // 开始过滤
                        $this->post[$name] = call_user_func_array(array($this->dfilter, $method), $param);
                    } else {
                        log_message('error', "过滤方法 $method 不存在！");
                    }
                } else {
                    // 函数格式：函数名称[:现存数据字段,参数2...]
                    list($func, $_param) = explode(':', str_replace('::', ':', $validate['filter']));
                    if (function_exists($func)) {
                        $param['value'] = $value;
                        if ($_param && $_value = explode(',', $_param)) {
                            foreach ($_value as $t) {
                                $param[$t] = isset($_POST['data'][$t]) ? $this->input->post("data[$t]") : $t;
                            }
                        }
                        // 开始过滤
                        $this->post[$name] = call_user_func_array($func, $param);
                    } else {
                        log_message('error', "过滤函数 $func 不存在！");
                    }
                }
            }
            // 判断表字段值的唯一性
            if ($field['ismain']
                && isset($field['setting']['option']['unique'])
                && $field['setting']['option']['unique']) {
                if ($this->db->where('id<>', (int)$_data['id'])->where($name, $this->post[$name])->count_all_results(SITE_ID.'_'.$this->mid)) {
                    return array('error' => $name, 'msg' => fc_lang('%s已经存在', $field['name']));
                }
            }
            $obj->insert_value($field); // 获取入库值
            if ($field['fieldtype'] == 'Baidumap') {
                $this->data[$field['ismain']][$name.'_lng'] = (double)$this->data[$field['ismain']][$name.'_lng'];
                $this->data[$field['ismain']][$name.'_lat'] = (double)$this->data[$field['ismain']][$name.'_lat'];
            } else {
                if (strpos($field['setting']['option']['fieldtype'], 'INT') !== FALSE) {
                    $this->data[$field['ismain']][$name] = (int)$this->data[$field['ismain']][$name];
                } elseif ($field['setting']['option']['fieldtype'] == 'DECIMAL'
                    || $field['setting']['option']['fieldtype'] == 'FLOAT') {
                    $this->data[$field['ismain']][$name] = (double)$this->data[$field['ismain']][$name];
                }
            }
        }
        //echo '<pre>'; print_r($this->data);exit;
        unset($this->post, $this->_data);
        return $this->data;
    }

    /**
     * 表单提交数据后执行的方法
     *
     * @param	intval	$id		主键id
     * @param	array	$_field	字段
     * @param	array	$_data	修改后的数据
     * @return  array
     */
    protected function validate_table($id, $_field, $_data = array()) {

        if (!$_field) {
            return;
        }

        $this->data = $_data;
        $this->load->library('Dfield', array(APP_DIR));
        foreach ($_field as $field) {
            // 验证字段对象的有效性
            $obj = $this->dfield->get($field['fieldtype']);
            if (!$obj) {
                continue;
            }
            // 执行脚本
            $obj->insert_last_value($id, $_data[$field['ismain']][$field['fieldname']], $field);
        }
    }

    /**
     * 附件处理
     *
     * @param	intval	$uid		会员uid
     * @param	array	$field		表字段
     * @param	string	$related	使用源的标识（表名-ID1-ID2...）
     * @param	array	$_data		原数据
     * @param	bool	$update		是否更新原附件
     * @return	NULL
     */
    public function attachment_handle($uid, $related, $field, $_data = NULL, $update = TRUE) {

        if (!$field) {
            return NULL;
        }

        // 当前POST的数据
        $attach = array();
        $attach_id = array();
        $this->load->model('attachment_model');
        $this->load->library('Dfield', array(APP_DIR));

        // 查询使用的附件
        foreach ($field as $f) {
            // 加载字段处理对象
            $obj = $this->dfield->get($f['fieldtype']);
            if (!$obj) {
                continue;
            }
            $files = $obj->get_attach_id($this->data[$f['ismain']][$f['fieldname']]);
            $files && $attach_id = $attach_id ? array_merge($attach_id, $files) : $files;
        }

        // 筛选删除的附件
        foreach ($field as $f) {
            // 加载字段处理对象
            $obj = $this->dfield->get($f['fieldtype']);
            if (!$obj) {
                continue;
            }
            // 通过附件处理方法获得增加和删除的附件
            list($add_id, $del_id) = $obj->attach($this->data[$f['ismain']][$f['fieldname']], $_data[$f['fieldname']]);
            // 百度编辑器时暂时不同步删除附件，可能会出现误删除情况
            $f['fieldtype'] == 'Ueditor' && $del_id = array();
            // 删除附件
            if ($del_id) {
                foreach ($del_id as $id) {
                    if ($id && $update && !in_array($id, $attach_id)) {
                        $fj = get_attachment($id);
                        $log = dr_now_url().'  --->>>  删除附件('.$id.'#'.$related.'#'.$f['fieldname'].')：'.$fj['attachment'];
                        $this->system_log($log, 1); // 强制写入后台日志
                        $this->attachment_model->delete_for_handle($uid, $related, $id);
                    }
                }
            }
            // 无新增附件时跳过
            if (!$add_id) {
                continue;
            }
            $attach = $attach ? array_merge($attach, $add_id) : $add_id;
        }

        $attach = $attach ? array_merge($attach, $attach_id) : $attach_id;
        if (count($attach) == 0) {
            return NULL;
        }

        // 更新至已用附件表
        $this->attachment_model->replace_attach($uid, $related, array_unique($attach));

        unset($this->data);

        return NULL;
    }


    /**
     * 附件归属替换
     *
     * @param	intval	$uid		会员uid
     * @param	intval	$id			使用源id
     * @param	string	$related	使用源表名称
     * @return	NULL
     */
    protected function attachment_replace($uid, $id, $related) {

        if (!$uid || !$id || !$related) {
            return NULL;
        }

        $data = $this->db->where('related', $related.'_verify-'.$id)->select('id,tableid')->get('attachment')->result_array();
        if (!$data) {
            return NULL;
        }

        foreach ($data as $t) {
            $this->db->where('id', $t['id'])->update('attachment', array('related' => $related.'-'.$id));
            $this->db->where('id', $t['id'])->update('attachment_'.(int)$t['tableid'], array('related' => $related.'-'.$id));
        }

        return NULL;
    }

    /**
     * 判断站点表是否重复
     *
     * @param	string	$tablename
     * @return	bool
     */
    public function site_table_exitis($tablename) {

        $num = $this->db->where('dirname', $tablename)->count_all_results('module');
        if ($num) {
            return TRUE;
        }

        // 库中筛选
        $table = $this->system_model->cache();
        if (isset($table[$this->db->dbprefix($tablename)])) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 字段输出表单（新版本格式）
     *
     * @param	array	$field	字段数组
     * @param	array	$data	表单值
     * @param	bool	$cat	是否显示栏目附加字段
     * @param	string	$id		id字符串
     * @param	string	$html   html布局格式
     * @param	bool	$is_diy	是否仅输出自定义输出字段
     * @return	string
     */
    public function new_field_input($field, $data = NULL, $cat = FALSE, $id = 'id', $html = '', $is_diy = 0) {

        if (!$field) {
            return '';
        }

        $id = $id ? $id : 'id';
        $mymerge = $merge = $group = $diyfield = array();
        $myfield = $mygroup = $mark = '';


        // 分组字段筛选
        foreach ($field as $t) {
            if ($t['fieldtype'] == 'Group'
                && preg_match_all('/\{(.+)\}/U', $t['setting']['option']['value'], $value)) {
                foreach ($value[1] as $v) {
                    $group[$v] = $t['fieldname'];
                }
            }
        }

        // 字段合并分组筛选
        foreach ($field as $t) {
            if ($t['fieldtype'] == 'Merge'
                && preg_match_all('/\{(.+)\}/U', $t['setting']['option']['value'], $value)) {
                foreach ($value[1] as $v) {
                    $merge[$v] = $t['fieldname'];
                }
            }
        }

        // 字段类
        $this->load->library('dfield', array(APP_DIR));
        $pchtml = $this->get_cache('member', 'setting', 'field');
        $mbhtml = $this->get_cache('member', 'setting', 'mbfield');
        if (!IS_ADMIN) {
            if ($this->mobile && $mbhtml) {
                // 移动端格式
                A_Field::set_input_format($mbhtml);
                unset($mbhtml);
            } elseif ($pchtml) {
                // Pc端格式
                A_Field::set_input_format($pchtml);
                unset($pchtml);
            }
        }

        // 主字段
        foreach ($field as $t) {
            if (!IS_ADMIN && !$t['ismember']) {
                continue;
            } elseif (IS_ADMIN && $this->member['adminid'] > 1
                && @in_array($this->member['adminid'], $t['setting']['show_admin'])) {
                continue;
            }
            $obj = $this->dfield->get($t['fieldtype']);
            $html && $obj->set_input_format($html);
            // 判断自定义输出字段
            if (!$is_diy && $t['setting']['is_right'] == 2) {
                $diyfield[$t['fieldname']] = $t;
                continue;
            }
            if (is_object($obj)) {
                // 百度地图特殊字段
                $obj->remove_div = 0;
                $value = $t['fieldtype'] == 'Baidumap' ? ($data[$t['fieldname'].'_lng'] && $data[$t['fieldname'].'_lat'] ? $data[$t['fieldname'].'_lng'].','.$data[$t['fieldname'].'_lat'] : $data[$t['fieldname']]) : $data[$t['fieldname']];

                if (isset($group[$t['fieldname']])) {
                    // 属于分组字段,重新获取字段表单
                    $obj->remove_div = 1;
                    $mygroup[$t['fieldname']] = $obj->input($t['name'], $t['fieldname'], $t['setting'], $value, isset($data[$id]) ? $data[$id] : 0);
                } elseif (isset($merge[$t['fieldname']])) {
                    // 属于合并字段
                    $input = $obj->input($t['name'], $t['fieldname'], $t['setting'], $value, isset($data[$id]) ? $data[$id] : 0);
                    $mymerge[$t['fieldname']] = $input;
                } elseif ($t['fieldtype'] == 'Merge') {
                    $myfield.= '{merge_'.$t['fieldname'].'}';
                } else {
                    $input = $obj->input($t['name'], $t['fieldname'], $t['setting'], $value, isset($data[$id]) ? $data[$id] : 0);
                    $myfield.= $input;
                }
            }
        }

        if ($merge) {
            $html = '

					    </div>
					</div>
				</div>
                <div class="portlet light bordered" id="dr_{name}">
                    <div class="portlet-title mytitle">
                        <div class="caption"><span class="caption-subject font-green">{text}</span></div>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
{value}';
            if (!IS_ADMIN) {
                $pchtml = $this->get_cache('member', 'setting', 'mergefield');
                $mbhtml = $this->get_cache('member', 'setting', 'mbmergefield');
                if ($this->mobile && $mbhtml) {
                    $html = $mbhtml;
                } elseif ($pchtml) {
                    // Pc端格式
                    $html = $pchtml;
                }
            }

            $data = array();
            foreach ($merge as $fname => $mname) {
                $data[$mname][] = $fname;
            }
            foreach ($data as $mname => $value) {
                $code = '';
                if ($value) {
                    foreach ($value as $fname) {
                        $code.= $mymerge[$fname];
                    }
                    $myfield = $code ? str_replace(
                        '{merge_'.$mname.'}',
                        str_replace(
                            array('{text}', '{name}', '{value}'),
                            array(fc_lang($field[$mname]['name']), $mname, $code),
                            $html
                        ),
                        $myfield
                    ) : str_replace(
                        '{merge_'.$mname.'}',
                        '',
                        $myfield
                    );
                }

            }
        }

        if ($mygroup) {
            foreach ($mygroup as $name => $t) {
                $myfield = str_replace('{'.$name.'}', $t, $myfield);
            }
        }

        // 存在自定义输出地址
        $diyfield && $this->template->assign('diyfield', $this->new_field_input($diyfield, $data, $cat, $id, $html, 1));

        return $myfield;
    }

    /**
     * 字段输出表单（兼容老版本）
     *
     * @param	array	$field	字段数组
     * @param	array	$data	表单值
     * @param	bool	$cat	是否显示栏目附加字段
     * @param	string	$id		id字符串
     * @return	string
     */
    public function field_input($field, $data = NULL, $cat = FALSE, $id = 'id') {

        return $this->new_field_input($field, $data, $cat, $id);
    }

    /**
     * 字段输出格式化
     *
     * @param	array	$fields 	可用字段集
     * @param	array	$data		数据
     * @param	intval	$curpage	分页id
     * @param	string	$dirname	模型目录
     * @return	string
     */
    public function field_format_value($fields, $data, $curpage = 1, $dirname = NULL) {

        if (!$fields
            || !$data
            || !is_array($data)) {
            return $data;
        }

        foreach ($data as $n => $value) {
            if (isset($fields[$n]) && $fields[$n]) {
                $format = dr_get_value($fields[$n]['fieldtype'], $value, $fields[$n]['setting']['option'], $dirname);
                if ($format !== $value) {
                    $data['_'.$n] = $value;
                    $data[$n] = $format;
                } elseif (SITE_MOBILE !== TRUE
                    && $n == 'content' && $fields[$n]['fieldtype'] == 'Ueditor'
                    && strpos($value, '<div name="dr_page_break" class="pagebreak">') !== FALSE
                    && preg_match_all('/<div name="dr_page_break" class="pagebreak">(.*)<\/div>/Us', $value, $match)
                    && preg_match('/(.*)<div name="dr_page_break"/Us', $value, $frist)) {
                    // 编辑器分页 老版本
                    $page = 1;
                    $content = $title = array();
                    $data['_'.$n] = $value;
                    $content[$page]['title'] = fc_lang('第%s页', $page);
                    $content[$page]['body'] = $frist[1];
                    foreach ($match[0] as $i => $t) {
                        $page ++;
                        $value = str_replace($content[$page - 1]['body'].$t, '', $value);
                        $body = preg_match('/(.*)<div name="dr_page_break"/Us', $value, $match_body) ? $match_body[1] : $value;
                        $title[$page] = trim($match[1][$i]);
                        $content[$page]['title'] = trim($match[1][$i]) ? trim($match[1][$i]) : fc_lang('第%s页', $page);
                        $content[$page]['body'] = $body;
                    }
                    $page = max(1, min($page, $curpage));
                    $data[$n] = $content[$page]['body'];
                    $data[$n.'_page'] = $content;
                    $data[$n.'_title'] = $title[$page];
                } elseif (SITE_MOBILE !== TRUE
                    && $fields[$n]['fieldtype'] == 'Ueditor'
                    && strpos($value, '<p class="pagebreak">') !== FALSE
                    && preg_match_all('/<p class="pagebreak">(.*)<\/p>/Us', $value, $match)
                    && preg_match('/(.*)<p class="pagebreak">/Us', $value, $frist)) {
                    // 编辑器分页 新版
                    $page = 1;
                    $content = $title = array();
                    $data['_'.$n] = $value;
                    $content[$page]['title'] = fc_lang('第%s页', $page);
                    $content[$page]['body'] = $frist[1];
                    foreach ($match[0] as $i => $t) {
                        $page ++;
                        $value = str_replace($content[$page - 1]['body'].$t, '', $value);
                        $body = preg_match('/(.*)<p class="pagebreak"/Us', $value, $match_body) ? $match_body[1] : $value;
                        $title[$page] = trim($match[1][$i]);
                        $content[$page]['title'] = trim($match[1][$i]) ? trim($match[1][$i]) : fc_lang('第%s页', $page);
                        $content[$page]['body'] = $body;
                    }
                    $page = max(1, min($page, $curpage));
                    $data[$n] = $content[$page]['body'];
                    $data[$n.'_page'] = $content;
                    $data[$n.'_title'] = $title[$page];
                }
            } elseif (strpos($n, '_lng') !== FALSE) {
                // 百度地图
                $name = str_replace('_lng', '', $n);
                $data[$name] = isset($data[$name.'_lat']) && ($data[$name.'_lng'] > 0 || $data[$name.'_lat'] > 0) ? $data[$name.'_lng'].','.$data[$name.'_lat'] : '';
            }
        }

        return $data;
    }





    /**
     * 邮件直接发送
     *
     * @param	string	$tomail
     * @param	string	$subject
     * @param	string	$message
     * @return  bool
     */
    public function sendmail($tomail, $subject, $message) {
        return $this->member_model->sendmail($tomail, $subject, $message);
    }


    /**
     * 检查目录可写
     *
     * @param	string	$pathfile
     * @return	boolean
     */
    protected function _check_write_able($pathfile) {

        if (!$pathfile) {
            return FALSE;
        }

        $isDir = in_array(substr($pathfile, -1), array('/', '\\')) ? TRUE : FALSE;
        if ($isDir) {
            if (is_dir($pathfile)) {
                mt_srand((double) microtime() * 1000000);
                $pathfile = $pathfile.'dr_'.uniqid(mt_rand()).'.tmp';
            } elseif (@mkdir($pathfile)) {
                return self::_check_write_able($pathfile);
            } else {
                return FALSE;
            }
        }

        @chmod($pathfile, 0777);
        $fp = @fopen($pathfile, 'ab');
        if ($fp === FALSE) {
            return FALSE;
        }

        fclose($fp);
        $isDir && @unlink($pathfile);

        return TRUE;
    }




    // 执行sql
    public function sql_query($sql, $db = NULL) {

        if (!$sql) {
            return NULL;
        }

        $sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $sql)));

        foreach ($sql_data as $query) {
            if (!$query) {
                continue;
            }
            $ret = '';
            $queries = explode('SQL_FINECMS_EOL', trim($query));
            foreach ($queries as $query) {
                $ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
            }
            if (!$ret) {
                continue;
            }
            $this->db->query($ret);
        }
    }

    ////////////////////////////////////////////////////////////////

    /**
     * 站点间的同步退出
     */
    protected function api_synlogin() {

        $code = dr_authcode(str_replace(' ', '+', $this->input->get('code')));
        if (!$code) {
            exit('code is null');
        }

        list($uid, $salt) = explode('-', $code);

        if (!$uid || !$salt) {
            exit('data is null');
        }

        $member = $this->db->select('password,salt')->where('uid', $uid)->get('member')->row_array();
        if (!$member) {
            exit('check error');
        } elseif ($member['salt'] != $salt) {
            exit('error');
        }

        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $expire = max((int)$this->input->get('expire'), 86400);

        ob_start();
        set_cookie('member_uid', $uid, $expire);
        set_cookie('member_cookie', substr(md5(SYS_KEY.$member['password']), 5, 20), $expire);
        ob_get_clean();
        exit('ok uid='.$uid);
    }

    /**
     * 站点间的同步登录
     */
    protected function api_synlogout() {
        if ($this->session->userdata('member_auth_uid')) {
            $this->session->unset_userdata('member_auth_uid');
        } else {
            set_cookie('member_uid', 0, -1);
            set_cookie('member_cookie', '', -1);

        }
    }

    /**
     * 自定义信息JS调用
     */
    protected function api_template() {

        ob_start();
        $this->template->cron = 0;
        $_GET['page'] = max(1, (int)$this->input->get('page'));
        $get = @json_decode(urldecode($this->input->get('get')), TRUE);
        $params = @json_decode(urldecode($this->input->get('params')), TRUE);
        $this->template->assign(array(
            'get' => $get,
            'params' => $params,
            'dirname' => $this->input->get('dirname')
        ));
        $this->template->assign($get);
        $this->template->assign($params);
        $name = str_replace(array('\\', '/', '..', '<', '>'), '', dr_safe_replace($this->input->get('name', TRUE)));
        $this->template->display(strpos($name, '.html') ? $name : $name.'.html');
        $html = ob_get_contents();
        ob_clean();

        // 格式输出
        if (isset($_GET['return']) && $_GET['return'] == 'js') {
            $html = addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html));
            echo 'document.write("'.$html.'");';
        } else {
            exit($html);
        }
        exit;
    }

    /**
     * 收藏文档
     */
    protected function api_favorite() {

        if (!$this->uid) {
            // 未登录
            if (isset($_GET['jsonp']) && $_GET['jsonp']) {
                echo dr_safe_replace(dr_safe_replace($this->input->get('callback', TRUE))).'('.json_encode(array('code' => '1')).')';exit;
            } else {
                exit('1');
            }
        }

        $id = (int)$this->input->get('id');
        $cid = (int)$this->input->get('cid');
        $mid = $cid ? $cid : $id; // 内容表id
        $eid = $cid ? $id : 0; // 扩展表id
        $data = $this->db->where('id', $mid)->select('url,title')->get(SITE_ID.'_'.APP_DIR)->row_array();
        if (!$data) {
            // 文档不存在
            if (isset($_GET['jsonp']) && $_GET['jsonp']) {
                echo dr_safe_replace(dr_safe_replace($this->input->get('callback', TRUE))).'('.json_encode(array('code' => '2')).')';exit;
            } else {
                exit('2');
            }
        }

        $table = SITE_ID.'_'.APP_DIR.'_favorite';
        $favorite = $this->db->where('cid', $mid)->where('uid', $this->uid)->where('eid', $eid)->select('id')->get($table)->row_array();
        if ($eid) {
            // 收藏扩展表
            $data2 = $this->db->where('cid', $mid)->get(SITE_ID.'_'.APP_DIR.'_extend')->row_array();
            if ($favorite) {
                $this->db->where('id', $favorite['id'])->update($table, array(
                    'url' => $data2['url'],
                    'title' => $data['title'].' - '.$data2['name']
                ));
                // 更新成功
                if (isset($_GET['jsonp']) && $_GET['jsonp']) {
                    echo dr_safe_replace(dr_safe_replace($this->input->get('callback', TRUE))).'('.json_encode(array('code' => '3')).')';exit;
                } else {
                    exit('3');
                }
            } else {
                // 添加成功
                $this->db->insert($table, array(
                    'eid' => $eid,
                    'cid' => $mid,
                    'uid' => $this->uid,
                    'url' => $data2['url'],
                    'title' => $data['title'].' - '.$data2['name'],
                    'inputtime' => SYS_TIME,
                ));
                // 更新数量
                $c = $this->db->where('eid', $eid)->count_all_results($table);
                $this->db->where('id', $eid)->set('favorites', $c)->update(SITE_ID.'_'.APP_DIR.'_extend');
                if (isset($_GET['jsonp']) && $_GET['jsonp']) {
                    echo dr_safe_replace(dr_safe_replace($this->input->get('callback', TRUE))).'('.json_encode(array('code' => '4')).')';exit;
                } else {
                    exit('4');
                }
            }
        } else {
            // 收藏主表
            if ($favorite) {
                $this->db->where('id', $favorite['id'])->update($table, array(
                    'url' => $data['url'],
                    'title' => $data['title']
                ));
                // 更新成功
                if (isset($_GET['jsonp']) && $_GET['jsonp']) {
                    echo dr_safe_replace(dr_safe_replace($this->input->get('callback', TRUE))).'('.json_encode(array('code' => '3')).')';exit;
                } else {
                    exit('3');
                }
            } else {
                // 添加成功
                $this->db->insert($table, array(
                    'eid' => 0,
                    'cid' => $mid,
                    'uid' => $this->uid,
                    'url' => $data['url'],
                    'title' => $data['title'] ? $data['title'] : '',
                    'inputtime' => SYS_TIME,
                ));
                // 更新数量
                $c = $this->db->where('cid', $mid)->count_all_results($table);
                $this->db->where('id', $mid)->set('favorites', $c)->update(SITE_ID.'_'.APP_DIR);
                if (isset($_GET['jsonp']) && $_GET['jsonp']) {
                    echo dr_safe_replace(dr_safe_replace($this->input->get('callback', TRUE))).'('.json_encode(array('code' => '4')).')';exit;
                } else {
                    exit('4');
                }
            }
        }
    }


    /**
     * 引用404页面
     */
    public function goto_404_page($msg) {
        //header("status: 404 Not Found");
        $this->template->assign(array(
            'msg' => $msg,
            'meta_title' => $msg
        ));
        $this->template->display('404.html');exit;
    }

    /**
     * 伪静态404页面
     */
    public function s404() {

        // 这里是满足不同用户的需求而设定的参数
        // code=1时会提示路由信息错误, code=2时直接指向首页且不提示任何信息
        $code = 1;

        // 以下不改
        $code == 1 && $this->goto_404_page('无法找到对应的页面('.dr_safe_replace(DR_URI).'),可能您没有配置好路由规则');
        $code == 2 && $this->_indexc();
    }

    /**
     * 后台日志
     */
    protected function system_log($action, $insert = 0) {

        if (!$insert) {
            // 是否开启日志
            if (!SYS_LOG || !IS_ADMIN) {
                return NULL;
            }
        }

        $data = array(
            'ip' => $this->input->ip_address(),
            'uid' => $this->member['uid'],
            'time' => SYS_TIME,
            'action' => addslashes($action),
            'username' => $this->member['username'],
        );

        $path = WEBPATH.'cache/optionlog/'.date('Ym', SYS_TIME).'/';
        $file = $path.date('d', SYS_TIME).'.log';
        !is_dir($path) && dr_mkdirs($path);

        file_put_contents($file, PHP_EOL.dr_array2string($data), FILE_APPEND);
    }

    // 任意表字段信息
    public function field_table($table, $sid) {

        $name = 'table_'.$table.$sid;
        $data = $this->dcache->get($name);
        if (!$data) {
            $field = $this->db
                ->where('disabled', 0)
                ->where('relatedname', 'table-'.$table)
                ->where('relatedid', $sid)
                ->order_by('displayorder ASC, id ASC')
                ->get('field')
                ->result_array();
            if ($field) {
                foreach ($field as $f) {
                    $data[$f['fieldname']] = $f;
                }
            }
            $this->dcache->set($name, $data);
        }

        return $data;
    }



    // jsonp 格式返回
    public function return_jsonp($data) {

        echo dr_safe_replace(dr_safe_replace($this->input->get('callback', TRUE))).'('.$data.')';exit;
    }


    // 获取任意表的自定义字段
    public function get_mytable_field($table, $siteid = 0) {

        $name = 'mytable-'.$table.'-'.$siteid;
        $value = $this->get_cache($name);
        if (!$value) {
            $field = $this->db
                ->where('disabled', 0)
                ->where('relatedid', $siteid)
                ->where('relatedname', 'table-'.$table)
                ->order_by('displayorder ASC,id ASC')
                ->get('field')
                ->result_array();
            if ($field) {
                foreach ($field as $t) {
                    $t['setting'] = dr_string2array($t['setting']);
                    $value[$t['fieldname']] = $t;
                }
            }
            $this->dcache->set($name, $value);
        }

        return $value;
    }



    /**
     * 栏目下级或者同级栏目
     */
    protected function _related_cat($category, $catid) {

        if (!$category) {
            return array(NULL, NULL);
        }

        $cat = $category[$catid];
        $related = $parent = array();

        if ($cat['child']) {
            $parent = $cat;
            foreach ($category as $t) {
                $t['pid'] == $cat['id'] && $related[] = $t;
            }
        } elseif ($cat['pid']) {
            foreach ($category as $t) {
                if ($t['pid'] == $cat['pid']) {
                    $related[] = $t;
                    $parent = $cat['child'] ? $cat : $category[$t['pid']];
                }
            }
        } else {
            if (!$category) {
                return array(NULL, NULL);
            }
            $parent = $cat;
            foreach ($category as $t) {
                $t['pid'] == 0 && $related[] = $t;
            }
        }

        return array($parent, $related);
    }

    // 去除url中的域名
    protected function _remove_domain($url) {

        if (!$this->_temp['domain']) {
            $domain = require WEBPATH.'config/domain.php';
            foreach ($domain as $u => $i) {
                $this->_temp['domain'][] = 'http://'.$u.'/';
                $this->_temp['domain'][] = 'https://'.$u.'/';
            }
        }

        return str_replace($this->_temp['domain'], '', $url);
    }


    /******************模型继承部分*******************/

    // 初始化模型
    protected function _module_init($dir = '') {

        // 定义模型目录
        !$this->dir && $this->dir = $dir;

        // 检查模型
        if (!$this->module[$this->dir]) {
            $this->admin_msg(fc_lang('当前模型【'.$this->dir.'】不存在'));
        }

        // 模型常量
        define('MOD_DIR', $this->dir);
        define('MODULE_TITLE', $this->module[$this->dir]['site']['module_title']);

        $this->load->model('content_model');
        $this->content_model->init($this->module[$this->dir]);

        $this->template->assign(array(
            'module' => $this->module,
        ));
    }

    // 获取返回时的URL
    protected function _get_back_url($uri, $param = array()) {

        $name = md5($uri.$this->uid.SITE_ID.$this->input->ip_address().$this->input->user_agent());
        $value = $this->cache->file->get($name);
        return $value ? $value : dr_url($uri, $param);
    }

    // 设置返回时的URL, uri页面标识,param参数,nuri当前页优先
    protected function _set_back_url($uri, $param = array(), $nuri = '') {

        !is_array($param) && $param = array();
        $name = md5($uri.$this->uid.SITE_ID.$this->input->ip_address().$this->input->user_agent());
        $param['page'] = $_GET['page'];
        $this->cache->file->save($name, dr_url($nuri ? $nuri : $uri, $param), 3600);
    }

    // 替换语言
    public function replace_lang($string) {

        if ($this->replace_lang === 1) {
            $lang = array();
            $this->replace_lang = array();
            is_file(FCPATH.'dayrui/languages/'.SITE_LANGUAGE.'/replace_lang.php') && require FCPATH.'dayrui/language/'.SITE_LANGUAGE.'/replace_lang.php';
            is_file(APPPATH.'language/'.SITE_LANGUAGE.'/replace_lang.php') && require_once APPPATH.'language/'.SITE_LANGUAGE.'/replace_lang.php';
            if ($lang) {
                foreach ($lang as $name => $value) {
                    $this->replace_lang[1][] = $name;
                    $this->replace_lang[2][] = $value;
                }
            }
        }

        return $this->replace_lang ? str_replace($this->replace_lang[1], $this->replace_lang[2], $string) : $string;
    }


    public function html_thumb($p) {

        // 参数解析
        list($id, $width, $height, $water, $size) = explode('-', $p);

        $info = get_attachment($id); // 图片信息
        if (!$info) {
            return THEME_PATH.'admin/images/nopic.gif';
        }

        // 缓存文件
        $thumb_file = trim(SYS_THUMB_DIR, '/').'/'.md5("$id-$width-$height-$water-$size").'.jpg';

        if ($info && in_array($info['fileext'], array('jpg', 'gif', 'png', 'jpeg'))) {
            // 远程图片下载到本地缓存目录
            if (isset($info['remote']) && $info['remote']) {
                $file = WEBPATH.'cache/attach/'.SYS_TIME.'_'.basename($info['attachment']);
                if ($size) {
                    $info['attachment'] =  str_replace(
                        basename($info['attachment']),
                        basename($info['attachment'], '.'.$info['fileext']).'_'.$size.'.'.$info['fileext'],
                        $info['attachment']
                    );
                }
                file_put_contents($file, dr_catcher_data($info['attachment']));
            } else {
                if ($size) {
                    $file_size =  str_replace(
                        basename($info['attachment']),
                        basename($info['attachment'], '.'.$info['fileext']).'_'.$size.'.'.$info['fileext'],
                        $info['attachment']
                    );
                    $file = is_file(SYS_UPLOAD_PATH.'/'.$file_size) ? SYS_UPLOAD_PATH.'/'.$file_size : SYS_UPLOAD_PATH.'/'.$info['attachment'];
                } else {
                    $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
                }
            }
            if (!is_file($file)) {
                return THEME_PATH.'admin/images/nopic.gif';
            }
        } else {
            return THEME_PATH.'admin/images/nopic.gif';
        }

        // 处理宽高
        list($_width, $_height) = @getimagesize($file);
        $width = $width ? $width : $_width;
        $height = $height ? $height : $_height;

        // 站点配置信息
        $site = $this->get_cache('siteinfo', $info['siteid']);

        // 生成新图参数
        $config['width'] = $width;
        $config['height'] = $height;
        $config['create_thumb'] = TRUE;
        $config['source_image'] = $file;
        $config['new_image'] = WEBPATH.$thumb_file;
        $config['thumb_marker'] = '';
        $config['image_library'] = 'gd2';
        $config['dynamic_output'] = false; // 覆盖图片
        $config['maintain_ratio'] = (bool)$site['SITE_IMAGE_RATIO']; // 使图像保持原始的纵横比例

        // 水印判断
        if (isset($info['remote']) && $info['remote'] && !$site['SITE_IMAGE_REMOTE']
            ? FALSE : ((bool)$site['SITE_IMAGE_WATERMARK'] && $water ? TRUE : FALSE)) {
            // 水印参数
            $config['wm_type'] = $site['SITE_IMAGE_TYPE'] ? 'overlay' : 'text';
            $config['wm_vrt_offset'] = $site['SITE_IMAGE_VRTOFFSET'];
            $config['wm_hor_offset'] = $site['SITE_IMAGE_HOROFFSET'];
            $config['wm_vrt_alignment'] = $site['SITE_IMAGE_VRTALIGN'];
            $config['wm_hor_alignment'] = $site['SITE_IMAGE_HORALIGN'];
            // 文字模式
            $config['wm_text'] = $site['SITE_IMAGE_TEXT'];
            $config['wm_font_size'] = $site['SITE_IMAGE_SIZE'];
            $config['wm_font_path'] = WEBPATH.'statics/watermark/'.($site['SITE_IMAGE_FONT'] ? $site['SITE_IMAGE_FONT'] : 'default.ttf');
            $config['wm_font_color'] = $site['SITE_IMAGE_COLOR'] ? str_replace('#', '', $site['SITE_IMAGE_COLOR']) : '#000000';
            // 图片模式
            $config['wm_opacity'] = $site['SITE_IMAGE_OPACITY'] ? $site['SITE_IMAGE_OPACITY'] : 80;
            $config['wm_overlay_path'] = WEBPATH.'statics/watermark/'.($site['SITE_IMAGE_OVERLAY'] ? $site['SITE_IMAGE_OVERLAY'] : 'default.png');
            // 生成图片的临时文件
            $this->load->library('image_lib');
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            // 打开临时文件再水印
            $this->image_lib->full_src_path = $config['new_image'];
            $this->image_lib->watermark();
        } else {
            // 默认模式
            $this->load->library('image_lib');
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
        }

        return SITE_URL.$thumb_file;
    }

    /**
     * 图片处理2
     */
    public function html_thumb2($p) {

        list($id, $width, $height, $autocut) = explode('-', $p);

        $this->load->library('dthumb');

        // 是附件id时
        if (is_numeric($id)) {
            $info = get_attachment($id);
            if (!$info) {
                return THEME_PATH.'admin/images/nopic.gif';
            }
            // 输出图片的地址
            $display = trim(SYS_THUMB_DIR, '/').'/'.md5("index.php?c=image&m=thumb&p=$id-$width-$height-$autocut").'.jpg';
            // 远程图片下载到本地缓存目录
            if (isset($info['remote']) && $info['remote']) {
                $file = WEBPATH.'cache/attach/'.time().'_'.basename($info['attachment']);
                file_put_contents($file, dr_catcher_data($info['attachment']));
            } else {
                $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
            }
            unset($info);
        } else {

            return THEME_PATH.'admin/images/nopic.gif';
        }

        // 图片不存在时调用默认图片
        if (!is_file($file)) {
            return THEME_PATH.'admin/images/nopic.gif';
        }

        // 生成缩略图
        $this->dthumb->thumb($file, $display, $width, $height, '', $autocut);

        // 输出缩略图
        $this->dthumb->html(WEBPATH.$display);

        return SITE_URL.$display;
    }

    public function get_module($siteid = 0) {
        return $this->get_cache('module');
    }

    public function select_category($data, $id = 0, $str = '', $default = ' -- ', $onlysub = 0, $is_push = 0, $is_first = 0) {


        $tree = array();
        $first = 0; // 第一个可用栏目
        $string = '<select class=\'form-control\' '.$str.'>';

        $default && $string.= "<option value='0'>$default</option>";

        if (is_array($data)) {
            foreach($data as $t) {
                // 外部链接不显示
                $is_link = isset($t['setting']['linkurl']) && $t['setting']['linkurl'] ? 1 : (isset($t['tid']) && $t['tid'] == 2 ? 1 : 0);
                if ($is_link) {
                    continue;
                }
                // 单页且为最终单页不显示
                if (isset($t['tid']) && $t['tid'] == 0 && !$t['child']) {
                    continue;
                }
                // 验证权限
                    // 正常栏目权限
                    if ($is_push && $t['child'] == 0) {
                        if ($t['mid'] && $t['mid'] != $this->mid) {
                            continue;
                        }
                    }
                    // 选中操作
                    $t['selected'] = '';
                    if (is_array($id)) {
                        $t['selected'] = in_array($t['id'], $id) ? 'selected' : '';
                    } elseif(is_numeric($id)) {
                        $t['selected'] = $id == $t['id'] ? 'selected' : '';
                    }
                    // 是否可选子栏目
                    $t['html_disabled'] = $onlysub && $t['child'] != 0 ? 1 : 0;

                // 第一个可用子栏目
                $first == 0 && $t['child'] == 0 && $first = $t['id'];
                if (isset($t['permission'])) {
                    unset($t['permission']);
                }
                if (isset($t['setting'])) {
                    unset($t['setting']);
                }
                $tree[$t['id']] = $t;
            }
        }

        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $str2 = "<optgroup label='\$spacer \$name'></optgroup>";

        $this->load->library('dtree');
        $this->dtree->init($tree);

        $string.= $this->dtree->get_tree_category(0, $str, $str2);
        $string.= '</select>';

        if ($is_first) {
            $mark = "value='";
            $first2 = (int)substr($string, strpos($string, $mark) + strlen($mark));
            $first = $first2 ? $first2 : $first;
        }

        $data = $is_first ? array($string, $first) : $string;


        return $data;
    }


    protected function _cache_url() {

        $url = array(
            array(
                'url' => dr_url('site/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('站点')),
            ),

            array(
                'url' => dr_url('module/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('模型')),
            ),


            array(
                'url' => dr_url('form/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('表单')),
            ),

            array(
                'url' => dr_url('category/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('栏目')),
            ),


            array(
                'url' => dr_url('mail/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('邮件')),
            ),


            array(
                'url' => dr_url('urlrule/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('URL规则')),
            ),

            array(
                'url' => dr_url('linkage/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('联动菜单')),
            ),

            array(
                'url' => dr_url('block/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('自定义内容')),
            ),

            array(
                'url' => dr_url('member_setting/cache', array('admin' => 1)),
                'name' => fc_lang('%s缓存', fc_lang('会员')),
            ),
        );


        $local = $this->local_app();
        if ($local) {
            foreach ($local as $t) {
                if ($t['cache']) {
                    $url = array_merge($url, $t['cache']);
                }
            }
        }

        return $url;
    }

    function local_app() {

        $source_dir = FCPATH.'app/';
        if ($fp = @opendir($source_dir)) {

            $app = array();
            $source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

            while (FALSE !== ($file = readdir($fp))) {
                if ($file === '.' OR $file === '..' OR $file === 'test.php') {
                    continue;
                }
                if (strpos($file, '.php') !== false) {
                    $app[] = require $source_dir.$file;
                }
            }
            closedir($fp);
            return $app;
        }

        return array();
    }

    /**
     * 验证会员名称
     *
     * @param	string	$username
     * @return	NULL
     */
    protected function is_username($username) {

        if (!$username) {
            return fc_lang('会员名称格式不正确');
        }

        $setting = $this->get_cache('member', 'setting');
        if ($setting['regnamerule']
            && !preg_match($setting['regnamerule'], $username)) {
            return fc_lang('会员名称格式不正确');
        }

        if ($setting['regnotallow']
            && @in_array($username, explode(',', $setting['regnotallow']))) {
            return fc_lang('该会员名称系统禁止注册');
        }

        return NULL;
    }

    /**
     * 验证Email
     *
     * @param	string	$email
     * @return	NULL
     */
    protected function is_email($email) {

        if (!$email) {
            return fc_lang('邮箱格式不正确');
        }

        if (!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $email)) {
            return fc_lang('邮箱格式不正确');
        }

        return NULL;
    }

}
