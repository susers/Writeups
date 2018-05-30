<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */
class Member_setting extends M_Controller {

    /**
     * 配置
     */
    public function index() {
	
		$page = (int)$this->input->get('page');
		$result = 0;
		
		if (IS_POST) {
			$post = $this->input->post('data', true);
			$page = (int)$this->input->post('page');
			$this->member_model->member($post);
			$data = $post;
			$cache = $this->member_model->cache();
            $result = 1;
            $this->system_log('会员配置'); // 记录日志
		} else {
			$cache = $this->member_model->cache();
			$data = $cache['setting'];
        }

		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('功能配置') => array('member_setting/index', 'cog')
			)),
			'data' => $data,
			'page' => $page,
			'result' => $result,
            'synurl' => $cache['synurl'],
		));
		$this->template->display('setting_index.html');
    }
	

	/**
     * OAuth2授权登录
     */
	public function oauth() {
		
		$oauth = array('qq' => 'QQ', 'sina' => '微博', 'weixin' => '微信'); //
		$this->load->library('dconfig');
		$config = require WEBPATH.'config/oauth.php';
		
		if (IS_POST) {
			$cfg = array();
			$data = $this->input->post('data');
			foreach ($oauth as $i => $name) {
				$cfg[$i] = array(
					'key' => trim($data['key'][$i]),
					'use' => isset($data['use'][$i]) ? 1 : 0,
					'name' => $config[$i]['name'] ? $config[$i]['name'] : $name,
					'icon' => $config[$i]['icon'] ? $config[$i]['icon'] : $i,
					'secret' => trim($data['secret'][$i])
				);
			}
			$this->dconfig->file(WEBPATH.'config/oauth.php')->note('OAuth2授权登录')->to_require($cfg);
			$config = $cfg;
            $this->system_log('快捷登录配置'); // 记录日志
			$this->template->assign('result', fc_lang('配置文件更新成功'));
		}
		
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				'OAuth' => array('member/admin/setting/oauth', 'weibo'),
			)),
			'data' => $config,
			'oauth' => $oauth
		));
		$this->template->display('setting_oauth.html');
	}

	/**
     * 缓存
     */
    public function cache() {
		$site = $this->input->get('site') ? $this->input->get('site') : SITE_ID;
		$admin = (int)$this->input->get('admin');
		$this->member_model->cache($site);
		$admin or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
    }
}