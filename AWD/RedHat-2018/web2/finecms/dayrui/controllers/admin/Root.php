<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 *
 */

class Root extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('管理员管理') => array('admin/root/index', 'users'),
		    fc_lang('添加') => array('admin/root/add_js', 'plus-square')
		)));
    }
	
	/**
     * 管理员管理
     */
    public function index() {

		if (IS_POST && $_POST['action'] == 'del') {
			$ids = $this->input->post('ids');
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            } elseif (!$this->is_auth('admin/root/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
			foreach ($ids as $id) {
				if ($id == 1) {
					exit(dr_json(0, fc_lang('无法删除创始人管理权限')));
				}
				$this->member_model->del_admin($id);
			}
            $this->system_log('删除后台管理员【#'.@implode(',', $ids).'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), ''));
		}
		
		$this->template->assign('list', $this->member_model->get_admin_all((int)$this->input->get('roleid'), $this->input->post('keyword', TRUE)));
		$this->template->display('admin_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		$role = $this->dcache->get('role');
		
		if (IS_POST) {
		
			$data = $this->input->post('data', TRUE);
			
			$check = $this->db
                          ->select('uid,adminid')
                          ->where('username', $data['username'])
                          ->limit(1)
                          ->get($this->db->dbprefix('member'))
                          ->row_array();
			$uid = $check['uid'];
			
			if (!$check) { // 会员不存在时，需要注册
				$member = array(
					'username' => $data['username'],
					'password' => trim($data['password']),
					'phone' => $data['phone'] ? $data['phone'] : '',
					'email' => $data['email']
				);
				$uid = $this->member_model->register($member, 3);
				if ($uid == -1) {
					exit(dr_json(0, fc_lang('该会员【%s】已经被注册', $data['username']), 'username'));
				} elseif ($uid == -2) {
					exit(dr_json(0, fc_lang('邮箱格式不正确'), 'email'));
				} elseif ($uid == -3) {
					exit(dr_json(0, fc_lang('该邮箱【%s】已经被注册', $data['email']), 'email'));
				} elseif ($uid == -4) {
					exit(dr_json(0, fc_lang('同一IP在限制时间内注册过多'), 'username'));
				} elseif ($uid == -5) {
					exit(dr_json(0, fc_lang('UCSSO：会员名称不合法'), 'username'));
				} elseif ($uid == -6) {
					exit(dr_json(0, fc_lang('UCSSO：包含不允许注册的词语'), 'username'));
				} elseif ($uid == -7) {
					exit(dr_json(0, fc_lang('UCSSO：Email格式有误'), 'username'));
				} elseif ($uid == -8) {
					exit(dr_json(0, fc_lang('UCSSO：Email不允许注册'), 'username'));
				} elseif ($uid == -9) {
					exit(dr_json(0, fc_lang('UCSSO：Email已经被注册'), 'username'));
				} elseif ($uid == -10) {
					exit(dr_json(0, fc_lang('手机号码必须是11位的整数'), 'phone'));
				} elseif ($uid == -11) {
					exit(dr_json(0, fc_lang('该手机号码已经注册'), 'phone'));
				}
			}
			
			$menu = array();
			if ($data['usermenu']) {
				foreach ($data['usermenu']['name'] as $id => $v) {
					$v && $data['usermenu']['url'][$id] && $menu[$id] = array('name' => $v, 'url' => $data['usermenu']['url'][$id]);
                }
			}
			
			$insert	= array(
				'uid' => $uid,
				'realname' => $data['realname'],
				'usermenu' => dr_array2string($menu)
			);
			$update	= array('adminid' => 1);
            $this->system_log('添加后台管理员【#'.$uid.'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $this->member_model->insert_admin($insert, $update, $uid)));
		}
		
		$this->template->assign('role', $role);
		$this->template->display('admin_add.html');
    }

	/**
     * 修改
     */
    public function edit() {
	
		$uid = (int)$this->input->get('id');
		$data = $this->member_model->get_admin_member($uid);
		!$data && exit(fc_lang('对不起，数据被删除或者查询不存在'));

		
		if (IS_POST) {
			$menu = array();
			$data = $this->input->post('data', TRUE);
			 if ($data['usermenu']) {
				foreach ($data['usermenu']['name'] as $id => $v) {
					$v && $data['usermenu']['url'][$id] && $menu[$id] = array('name' => $v, 'url' => $data['usermenu']['url'][$id]);
                }
			}
			$insert	= array(
				'uid' => $uid,
				'realname' => $data['realname'],
				'usermenu' => dr_array2string($menu)
			);
			$update	= array('adminid' => 1);
            $this->system_log('修改后台管理员【#'.$uid.'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $this->member_model->update_admin($insert, $update, $uid)));
		}
		
		$this->template->assign(array(
			'data' => $data
		));
		$this->template->display('admin_add.html');
    }
	
	/**
     * 修改资料
     */
    public function my() {

		if (IS_POST) {
			$menu = array();
			$data = $this->input->post('data', TRUE);
			$password = trim($data['password']);
			if ($data['usermenu']) {
				foreach ($data['usermenu']['name'] as $id => $v) {
					$v && $data['usermenu']['url'][$id] && $menu[$id] = array('name' => $v, 'url' => $data['usermenu']['url'][$id]);
                }
			}
            // 修改密码
			if ($password) {
                if (defined('UCSSO_API')) {
                    $rt = ucsso_edit_password($this->uid, $password);
                    // 修改失败
                    if (!$rt['code']) {
                        $this->admin_msg(fc_lang($rt['msg']));
                    }
                }
				$this->db->where('uid', $this->uid)->update('member', array(
                    'password' => md5(md5($password).$this->member['salt'].md5($password))
                ));
			}
			$this->db->where('uid', $this->uid)->update('admin', array(
                'color' => $data['color'],
                'realname' => $data['realname'],
                'usermenu' => dr_array2string($menu)
            ));
            $this->system_log('修改后台管理员资料【#'.$this->uid.'】'); // 记录日志
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('root/my'), 1);
		} else {
			$this->template->display('admin_my.html');
		}
    }

	/**
     * 删除
     */
    public function del() {

        $id = (int)$this->input->get('id');
		// 认证权限
		if ($id == 1) {
			exit(dr_json(0, fc_lang('无法删除创始人管理权限')));
		}

		$this->member_model->del_admin($id);
        $this->system_log('删除后台管理员资料【#'.$id.'】'); // 记录日志
		exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
	}
	
	/**
     * 检查用户情况
     */
	public function check_username() {
		$result = $this->db
                       ->select('uid,adminid')
                       ->where('username', $this->input->post('username', TRUE))
                       ->limit(1)
                       ->get($this->db->dbprefix('member'))
                       ->row_array();
		!$result && exit(dr_json(1, ''));
        // 已经属于管理组
		exit(dr_json(0, '', $result['uid'])); // 已经注册会员
	}

}