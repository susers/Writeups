<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Login extends M_Controller {
    
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->output->enable_profiler(FALSE);
    }
    
    public function index() {


        $id = 0;
        $error = '';

		if (IS_POST) {
			if (SITE_ADMIN_CODE && !$this->check_captcha('code')) {
                $error = fc_lang('验证码不正确');
            } else {
				$u = $this->input->post('username', true);
				$p = $this->input->post('password', true);
                $uid = $this->member_model->admin_login($u, $p);
                if ($uid > 0) {
                    $url = $this->input->get('backurl') ? urldecode($this->input->get('backurl', true)) : dr_url('home');
                    $url = pathinfo($url);
                    $url = $url['basename'] ? $url['basename'] : SELF;
                    $this->admin_msg(fc_lang('登录成功'), $url, 1);
                }

				$this->system_log('登录后台失败，账号【'.$u.'】，密码【'.$p.'】', 1);
                if ($uid == -1) {
                    $id = 1;
                    $error = fc_lang('会员['.$u.']不存在');
                } elseif ($uid == -2) {
                    $error = fc_lang('密码['.$p.']不正确');
                } elseif ($uid == -3) {
                    $error = fc_lang('您无权限登录管理平台');
                } elseif ($uid == -4) {
                    $error = fc_lang('您无权限登录该站点');
                } else {
                    $error = fc_lang('未定义的操作');
                }
            }
		}
		
		$this->template->assign('id', $id);
		$this->template->assign('error', $error);
		$this->template->assign('username', $this->input->post('username', TRUE));
		$this->template->display('login.html');
    }

    public function ajax() {

        $data = $this->input->post('data', TRUE);
        $uid = $this->member_model->admin_login($data['username'], $data['password']);
        if ($uid > 0) {
            set_cookie('finecms-admin-login', $data['username'], 999999);
            exit(dr_json(1, 1, 1));
        }
        if ($uid == -1) {
            $error = fc_lang('会员不存在');
            exit(dr_json(0, $error, 'username'));
        } elseif ($uid == -2) {
            $error = fc_lang('密码不正确');
            exit(dr_json(0, $error, 'password'));
        } elseif ($uid == -3) {
            $error = fc_lang('您无权限登录管理平台');
            exit(dr_json(0, $error, 'username'));
        } elseif ($uid == -4) {
            $error = fc_lang('您无权限登录该站点');
            exit(dr_json(0, $error, 'username'));
        } else {
            $error = fc_lang('未定义的操作');
            exit(dr_json(0, $error, 'username'));
        }

    }

	public function logout() {
		$this->session->unset_userdata('admin');
		$this->session->unset_userdata('siteid');
		$this->admin_msg(fc_lang('您已经安全退出系统了'), dr_url(''), 1);
	}
}