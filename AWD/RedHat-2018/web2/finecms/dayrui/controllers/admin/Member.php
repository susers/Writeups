<?php


/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Member extends M_Controller {

    private $userinfo;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
			fc_lang('会员管理') => array('admin/member/index', 'user'),
			fc_lang('添加') => array('admin/member/add_js', 'plus')
		)));
    }

    /**
     * 首页
     */
    public function index() {

		if (IS_POST && $this->input->post('action')) {

            // ID格式判断
			$ids = $this->input->post('ids');
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
			
			if ($this->input->post('action') == 'del') {
                // 删除
                !$this->is_auth('member/admin/home/del') && exit(dr_json(0, fc_lang('您无权限操作')));
				$this->member_model->delete($ids);
                defined('UCSSO_API') && ucsso_delete($ids);
                $this->system_log('删除会员【#'.@implode(',', $ids).'】'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			} else {
                // 修改会员组
                !$this->is_auth('member/admin/home/edit') && exit(dr_json(0, fc_lang('您无权限操作')));
				$gid = (int)$this->input->post('groupid');
				$note = fc_lang('您的会员组由管理员%s改变成：%s', $this->member['username'], $this->get_cache('member', 'group', $gid, 'name'));
				$this->db->where_in('uid', $ids)->update('member', array('groupid' => $gid));

                foreach ($ids as $uid) {
                    // 会员组升级挂钩点
                    $this->hooks->call_hook('member_group_upgrade', array('uid' => $uid, 'groupid' => $gid));
                    // 表示审核会员
                    $this->member_model->update_admin_notice('member/admin/home/index/field/uid/keyword/'.$uid, 3);
                }
                $this->system_log('修改会员【#'.@implode(',', $ids).'】的会员组'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			}
		}

        // 重置页数和统计
        IS_POST && $_GET['page'] = $_GET['total'] = 0;
	
		// 根据参数筛选结果
        $param = $this->input->get(NULL, TRUE);
        unset($param['s'], $param['c'], $param['m'], $param['d'], $param['page']);
		
		// 数据库中分页查询
		list($data, $param) = $this->member_model->limit_page($param, max((int)$_GET['page'], 1), (int)$_GET['total']);


        $field = $this->get_cache('member', 'field');
        $field = array(
            'username' => array('fieldname' => 'username','name' => fc_lang('会员名称')),
            'name' => array('fieldname' => 'name','name' => fc_lang('姓名')),
            'email' => array('fieldname' => 'email','name' => fc_lang('会员邮箱')),
            'phone' => array('fieldname' => 'phone','name' => fc_lang('手机号码')),
        ) + ($field ? $field : array());

        // 存储当前页URL
        $this->_set_back_url('member/index', $param);

		$this->template->assign(array(
			'list' => $data,
            'field' => $field,
			'param'	=> $param,
			'pages'	=> $this->get_pagination(dr_url('member/index', $param), $param['total']),
		));
		$this->template->display('member_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {


        $MEMBER = $this->get_cache('member');

		if (IS_POST) {
		
			$all = $this->input->post('all');
			$info = $this->input->post('info');
			$data = $this->input->post('data');

			
			if ($all) {
				// 批量添加
                !$info && exit(dr_json(0, fc_lang('批量注册信息填写不完整'), 'info'));
				$data = explode(PHP_EOL, $info);
				$success = $error = 0;
				foreach ($data as $t) {
					list($username, $password, $email, $phone) = explode('|', $t);
					if ($username || $password || $email || $phone) {
						$uid = $this->member_model->register(array(
                            'phone' => $phone,
                            'email' => $email,
							'username' => $username,
							'password' => trim($password),
						), $data['groupid']);
						if ($uid > 0) {
							$success ++;
                            $this->system_log('添加会员【#'.$uid.'】'.$username); // 记录日志
						} else {
							$error ++;
						}
					}
				}
				exit(dr_json(1, fc_lang('批量注册成功%s，失败%s', $success, $error)));
			} else {
				// 单个添加
                $uid = $this->member_model->register(array(
                    'email' => $data['email'],
                    'phone' => $data['phone'] ? $data['phone'] : '',
                    'username' => $data['username'],
                    'password' => trim($data['password']),
                ), $data['groupid']);
                if ($uid == -1) {
                    exit(dr_json(0, fc_lang('该会员【%s】已经被注册', $data['username']), 'username'));
                } elseif ($uid == -2) {
                    exit(dr_json(0, fc_lang('邮箱格式不正确'), 'email'));
                } elseif ($uid == -3) {
                    exit(dr_json(0, fc_lang('该邮箱【%s】已经被注册', $data['email']), 'email'));
                } elseif ($uid == -4) {
                    exit(dr_json(0, fc_lang('同一IP在限制时间内注册过多'), 'username'));
                } elseif ($uid == -5) {
                    exit(dr_json(0, fc_lang('Ucenter：会员名称不合法'), 'username'));
                } elseif ($uid == -6) {
                    exit(dr_json(0, fc_lang('Ucenter：包含不允许注册的词语'), 'username'));
                } elseif ($uid == -7) {
                    exit(dr_json(0, fc_lang('Ucenter：Email格式有误'), 'username'));
                } elseif ($uid == -8) {
                    exit(dr_json(0, fc_lang('Ucenter：Email不允许注册'), 'username'));
                } elseif ($uid == -9) {
                    exit(dr_json(0, fc_lang('Ucenter：Email已经被注册'), 'username'));
                } elseif ($uid == -10) {
                    exit(dr_json(0, fc_lang('手机号码必须是11位的整数'), 'phone'));
                } elseif ($uid == -11) {
                    exit(dr_json(0, fc_lang('该手机号码已经注册'), 'phone'));
                } else {
                    $this->system_log('添加会员【#'.$uid.'】'.$data['username']); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                }
			}
		}
		
		$this->template->display('member_add.html');
    }
	
	/**
     * 修改
     */
    public function edit() {
	
		$uid = (int)$this->input->get('uid');
		$page = (int)$this->input->get('page');
		$data = $this->member_model->get_member($uid);

        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

		$field = array();
		$MEMBER = $this->get_cache('member');


		if ($MEMBER['field']) {
			foreach ($MEMBER['field'] as $t) {
                $field[] = $t;
			}
		}

		if (IS_POST) {
			$edit = $this->input->post('member');
			$page = (int)$this->input->post('page');
			$post = $this->validate_filter($field, $data);
			if (isset($post['error'])) {
				$error = $post['msg'];
			} else {
				$post[1]['uid'] = $uid;
				$post[1]['is_auth'] = (int)$data['is_auth'];
				$post[1]['complete'] = (int)$data['complete'];
				$this->db->replace('member_data', $post[1]);
				$this->attachment_handle($uid, $this->db->dbprefix('member').'-'.$uid, $field, $data);
				$update = array(
					'name' => $edit['name'],
					'phone' => $edit['phone'],
					'groupid' => $edit['groupid'],
				);
                // 修改密码
                $edit['password'] = trim($edit['password']);
				if ($edit['password']) {
                    if (defined('UCSSO_API')) {
                        $rt = ucsso_edit_password($uid, $edit['password']);
                        // 修改失败
                        if (!$rt['code']) {
                            $this->admin_msg(fc_lang($rt['msg']));
                        }
                    }
					$update['password'] = md5(md5($edit['password']).$data['salt'].md5($edit['password']));
                    $this->member_model->add_notice($uid, 1, fc_lang('您的密码被管理员%s修改了', $this->member['username']));
                    $this->system_log('修改会员【'.$data['username'].'】密码'); // 记录日志
				}
                // 修改邮箱
                if ($edit['email'] != $data['email']) {
                    !preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $edit['email']) && $this->admin_msg(fc_lang('邮箱格式不正确'));
                    $this->db->where('email', $edit['email'])->where('uid<>', $uid)->count_all_results('member') && $this->admin_msg(fc_lang('该邮箱【%s】已经被注册', $edit['email']));

                    if (defined('UCSSO_API')) {
                        $rt = ucsso_edit_email($uid, $edit['email']);
                        // 修改失败
                        if (!$rt['code']) {
                            $this->admin_msg(fc_lang($rt['msg']));
                        }
                    }
                    $update['email'] = $edit['email'];
                    $this->member_model->add_notice($uid, 1, fc_lang('您的注册邮箱被管理员%s修改了', $this->member['username']));
                    $this->system_log('修改会员【'.$data['username'].'】邮箱'); // 记录日志
                }
                // 修改手机
                if  ($edit['phone'] != $data['phone']) {
                    if (defined('UCSSO_API')) {
                        $rt = ucsso_edit_phone($uid, $edit['phone']);
                        // 修改失败
                        if (!$rt['code']) {
                            $this->admin_msg(fc_lang($rt['msg']));
                        }
                    }
                }
				$this->db->where('uid', $uid)->update('member', $update);

                $this->system_log('修改会员【'.$data['username'].'】资料'); // 记录日志
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('member/edit', array('uid' => $uid, 'page' => $page)), 1);
			}
			$this->admin_msg($error, dr_url('member/edit', array('uid' => $uid, 'page' => $page)));
		}
		
		$this->template->assign(array(
			'data' => $data,
			'page' => $page,
			'myfield' => $this->field_input($field, $data, TRUE),
		));
		$this->template->display('member_edit.html');
    }

    public function ajax_email() {

        $uid = (int)$this->input->get('uid');
        $email = $this->input->get('email');

        if (!$email || !preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $email)) {
            exit(fc_lang('邮箱格式不正确'));
        } elseif ($this->db->where('email', $email)->where('uid<>', $uid)->count_all_results('member')) {
            exit(fc_lang('该邮箱【%s】已经被注册', $email));
        }

        exit(0);
    }


}