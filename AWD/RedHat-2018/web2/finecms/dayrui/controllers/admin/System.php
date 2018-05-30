<?php
/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */
class System extends M_Controller {



    /**
     * 配置
     */
    public function config() {

        // 加载网站系统配置文件
        $data = require WEBPATH.'config/system.php';

        if (IS_POST) {
            $this->load->library('dconfig');
            $post = $this->input->post('data', true);
            $save = array(
                'SYS_DEBUG',
                'SYS_AUTO_CACHE',
                'SYS_CAT_MODULE',
                'SYS_HTTPS',
                'SYS_LOG',
                'SITE_ADMIN_CODE',
                'SITE_ADMIN_PAGESIZE',
                'SYS_EMAIL',
                'SYS_KEY',
            );
            foreach ($save as $key) {
                if ($key == 'SYS_KEY') {
                    $value = $post[$key] == '***' ? $data[$key] : $post[$key];
                } else {
                    $value = isset($post[$key]) ? $post[$key] : 0;
                    $value === 'TRUE' && $value = 1;
                }
                $data[$key] = $value;
            }

            $this->dconfig->file(WEBPATH.'config/system.php')->note('系统配置文件')->space(32)->to_require($data);

            $this->system_log('修改系统配置'); // 记录日志
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('system/'.$this->router->method), 1);
        }


        $this->template->assign(array(
            'page' => 0,
            'data' => $data,
            'menu' => $this->get_menu_v3(array(
                fc_lang('系统配置') => array('admin/system/config', 'cog'),
            ))
        ));
        $this->template->display('system_index.html');
    }
	
	/**
     * 系统操作日志
     */
    public function index() {

		$time = isset($_POST['data']['time']) && $_POST['data']['time'] ? (int)$_POST['data']['time'] : (int)$this->input->get('time');
        $time = $time ? $time : SYS_TIME;
        $file = WEBPATH.'cache/optionlog/'.date('Ym', $time).'/'.date('d', $time).'.log';

        $list = array();
        $data = @explode(PHP_EOL, file_get_contents($file));
        $data = @array_reverse($data);

        $page = IS_POST ? 1 : max(1, (int)$this->input->get('page'));
        $total = count($data);
        $limit = ($page - 1) * SITE_ADMIN_PAGESIZE;

        $i = $j = 0;

        foreach ($data as $v) {
            if ($v && $i >= $limit && $j < SITE_ADMIN_PAGESIZE) {
                $list[] = $v;
                $j ++;
            }
            $i ++;
        }

        $this->load->library('dip');
        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('操作日志') => array('admin/system/index', 'calendar'),
            ))
        ));

        $this->template->assign(array(
            'time' => $time,
            'list' => $list,
            'total' => $total,
            'pages'	=> $this->get_pagination(dr_url('system/index', array('time' => $time)), $total)
        ));
        $this->template->display('system_oplog.html');
	}

	/**
     * debug
     */
    public function debug() {

		$time = isset($_POST['data']['time']) && $_POST['data']['time'] ? (int)$_POST['data']['time'] : (int)$this->input->get('time');
        $time = $time ? $time : SYS_TIME;
        $total = 0;
        $file = WEBPATH.'cache/errorlog/log-'.date('Y-m-d', $time).'.php';
        if (is_file($file)) {


            $log = trim(str_replace("<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>", '', file_get_contents($file)), PHP_EOL);

            $data = @explode(PHP_EOL, str_replace(chr(10), PHP_EOL, $log));

            $data && $data = @array_reverse($data);

            $page = IS_POST ? 1 : max(1, (int)$this->input->get('page'));
            $total = count($data);
            $limit = ($page - 1) * SITE_ADMIN_PAGESIZE;

            $i = $j = 0;

            foreach ($data as $t) {
                if ($t && $i >= $limit && $j < SITE_ADMIN_PAGESIZE) {
                    $v = @explode(' --> ', $t);
                    $time2 = $v ? @explode(' - ', $v[0]) : array(1=>'');
                    $list[] = array(
                        'time' => $time2[1],
                        'error' => $v[1],
                    );
                    $j ++;
                }
                $i ++;
            }

        }

        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('错误日志') => array('admin/system/debug', 'bug'),
            ))
        ));
        $this->template->assign(array(
            'time' => $time,
            'list' => $list,
            'total' => $total,
            'pages'	=> $this->get_pagination(dr_url('system/debug', array('time' => $time)), $total)
        ));
        $this->template->display('system_debug.html');
	}

	/**
     * 生成安全码
     */
    public function syskey() {
		echo 'CI3'.strtoupper(substr((md5(SYS_TIME)), rand(0, 10), 13));exit;
	}

	/**
     * 生成来路随机字符
     */
    public function referer() {
		$s = strtoupper(base64_encode(md5(SYS_TIME).md5(rand(0, 2015).md5(rand(0, 2015)))).md5(rand(0, 2009)));
		echo str_replace('=', '', substr($s, 0, 64));exit;
	}


}