<?php


/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */


class Register extends M_Controller {


	/**
	 * 注册
	 */
	public function index() {

		// 会员配置
		$MEMBER = $this->get_cache('MEMBER');



		// 判断是否开启注册
		if (!$MEMBER['setting']['register']) {
			$this->member_msg(fc_lang('站点已经关闭了会员注册'));
		}

		if (IS_POST) {
			$data = $this->input->post('data', TRUE);
			$back_url =  dr_member_url('home/index');
			if ($MEMBER['setting']['regcode'] && !$this->check_captcha('code')) {
				$error = array('name' => 'code', 'msg' => fc_lang('验证码不正确'));
			} elseif ($result = $this->is_username($data['username'])) {
				$error = array('name' => 'username', 'msg' => $result);
			} elseif (!$data['password']) {
				$error = array('name' => 'password', 'msg' => fc_lang('密码不能为空'));
			} elseif ($data['password'] !== $data['password2']) {
				$error = array('name' => 'password2', 'msg' => fc_lang('两次密码输入不一致'));
			} elseif ($result = $this->is_email($data['email'])) {
				$error = array('name' => 'email', 'msg' => $result);
			} else {
				$id = $this->member_model->register($data, 0);
				if ($id > 0) {
					// 注册成功
					$data['uid'] = $id;
					$this->hooks->call_hook('member_register_after', $data); // 注册之后挂钩点
					// 注册后的登录
					$code = $this->member_model->login($id, $data['password'], $data['auto'] ? 8640000 : $MEMBER['setting']['loginexpire'], 0, 1);

				} elseif ($id == -1) {
					$error = array('name' => 'username', 'msg' => fc_lang('该会员【%s】已经被注册', $data['username']));
				} elseif ($id == -2) {
					$error = array('name' => 'email', 'msg' => fc_lang('邮箱格式不正确'));
				} elseif ($id == -3) {
					$error = array('name' => 'email', 'msg' => fc_lang('该邮箱【%s】已经被注册', $data['email']));
				} elseif ($id == -4) {
					$error = array('name' => 'username', 'msg' => fc_lang('同一IP在限制时间内注册过多'));
				} elseif ($id == -5) {
					$error = array('name' => 'username', 'msg' => fc_lang('UCSSO：会员名称不合法'));
				} elseif ($id == -6) {
					$error = array('name' => 'username', 'msg' => fc_lang('UCSSO：包含不允许注册的词语'));
				} elseif ($id == -7) {
					$error = array('name' => 'username', 'msg' => fc_lang('UCSSO：Email格式有误'));
				} elseif ($id == -8) {
					$error = array('name' => 'username', 'msg' => fc_lang('UCSSO：Email不允许注册'));
				} elseif ($id == -9) {
					$error = array('name' => 'username', 'msg' => fc_lang('UCSSO：Email已经被注册'));
				} elseif ($id == -10) {
					$error = array('name' => 'phone', 'msg' => fc_lang('手机号码必须是11位的整数'));
				} elseif ($id == -11) {
                    $error = array('name' => 'phone', 'msg' => fc_lang('该手机号码已经注册'));
                } else {
					$error = array('name' => 'username', 'msg' => fc_lang('注册失败'));
				}
			}
			if (IS_AJAX) {
				$error && exit(dr_json(0, $error['msg']));
				$id > 0 && exit(json_encode(array(
					'status' => 1,
					'backurl' => $back_url,
					'syncurl' => dr_member_sync_url($code))));
			}
			$code && $this->member_msg(fc_lang('注册成功').$code, $back_url, 1, 3);
            exit;
		} else {
			$data = array();
		}

		$this->template->assign(array(
			'data' => $data,
			'code' => $MEMBER['setting']['regcode'],
			'back_url' => $back_url,
			'meta_title' => fc_lang('会员注册'),
		));
		$this->template->display('register.html');
	}
}