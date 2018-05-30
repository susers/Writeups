<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

require FCPATH.'dayrui/core/M_Table.php';

class Wuser extends M_Table {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('weixin_model');
        $this->mydb = $this->db; // 数据库
        $this->tfield = 'subscribe_time';
        $this->mytable = $this->weixin_model->prefix.'_user';
        $this->myfield = $field = array(
            'uid' => array(
                'name' => 'Uid',
                'ismain' => 1,
                'fieldname' => 'uid',
            ),
            'username' => array(
                'name' => '会员账号',
                'ismain' => 1,
                'fieldname' => 'username',
            ),
        );
        $this->template->assign(array(
            'field' => $field,
            'menu' => $this->get_menu_v3(array(
                '粉丝管理' => array('admin/wuser/index', 'user'),
                '与微信公众平台同步' => array('admin/wuser/syc', 'refresh'),
            )),
        ));
    }

    /**
     * 微信粉丝管理
     */
    public function index() {

        if (IS_POST &&  $_POST['data']['field'] == 'nickname') {
            $key = $_POST['data']['keyword'];
            $key2 = str_replace ( '\u', '\\\\\\\\u', trim ( dr_deal_emoji ($key, 0 ), '"' ) );

            // 搜索用户表
            $this->mywhere = "(nickname LIKE '%$key%' OR nickname LIKE '%$key2%')";
        } elseif (IS_POST && $this->input->post('action') == 'order') {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            $gid = (int)$this->input->post('gid');
            $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . dr_get_access_token ();
            foreach ($ids as $id) {
                $param = array();
                $param ['openid'] = $id;
                $param ['to_groupid'] = $gid;
                $param = JSON ( $param );
                dr_post_data( $url, $param );
            }
            $this->db->where_in('openid', $ids)->update($this->weixin_model->prefix.'_user', array('groupid' => $gid));
            exit(dr_json(1, fc_lang('更改分组成功')));
        } elseif (IS_POST && $this->input->post('action') == 'del') {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            $this->db->where_in('openid', $ids)->delete($this->weixin_model->prefix.'_user');
            $this->db->where_in('openid', $ids)->delete($this->weixin_model->prefix.'_follow');
            exit(dr_json(1, fc_lang('成功删除粉丝')));
        }

        // 数据库中分页查询
		list($data, $total, $param)	= $this->limit_page();
		$param['total'] = $total;
		$this->template->assign(array(
			'list' => $data,
			'total' => $total,
			'pages'	=> $this->get_pagination(dr_url(APP_DIR.'/'.$this->router->class.'/'.$this->router->method, $param), $total),
			'param' => $param,
		));

        $this->template->display('wuser_index.html');
    }

    public function syc() {

        $action = $this->input->get('action');
        if (!$action) {
            $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.dr_get_access_token().'&next_openid='.$this->input->get('next_openid');
            $data = @json_decode(@wx_get_https_json_data ($url), true);
            if (!isset($data['count']) || $data['count'] == 0) {
                // 拉取完毕 全部设置为0状态
                $this->db->update($this->weixin_model->prefix.'_follow', array(
                    'status' => 0,
                ));
                $this->admin_msg('同步用户数据中，请勿关闭', dr_url('wuser/syc', array('action'=>'user')),1,0);
            }

            // 查询并入库
            $res = $this->db->where_in('openid', $data ['data'] ['openid'])->get($this->weixin_model->prefix.'_follow')->result_array();

            if (count($res) != $data ['count']) {
                // 更新的数量不一致，可能有增加的用户openid
                $openids = array();
                if ($res) {
                    foreach ($res as $t) {
                        $openids[] = $t['openid'];
                    }
                }
                $diff = array_diff ( $data ['data'] ['openid'], $openids );
                if (! empty ( $diff )) {
                    foreach ( $diff as $id ) {
                        $save =array();
                        $save ['openid'] = $id;
                        $save ['uid'] = 0;
                        $save ['status'] = 0;
                        $this->db->insert($this->weixin_model->prefix.'_follow', $save);
                    }
                }
            }
            $this->admin_msg ( '同步用户OpenID中，请勿关闭', dr_url('wuser/syc', array('next_openid' => $data ['next_openid'])), 1, 0);
        } elseif ($action == 'user') {
            // 开始更新会员资料
            $list = $this->db->where('status', 0)->limit(50)->get($this->weixin_model->prefix.'_follow')->result_array();
            if (empty ( $list )) {
                $this->admin_msg('同步用户完成', dr_url('wuser/index'),1,0);
                exit ();
            }
            $param = $openids = $uids = array();
            foreach ( $list as $vo ) {
                $param ['user_list'] [] = array (
                    'openid' => $vo ['openid'],
                    'lang' => 'zh-CN'
                );
                $openids[] = $vo ['openid'];
                $uids [$vo ['openid']] = $vo ['uid'];
            }
            $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . dr_get_access_token ();
            $data = dr_post_data ( $url, $param );
            if (isset($data['errcode']) && $data['errcode'] != 0) {
                $this->admin_msg(dr_error_msg($data));
            }
            $update = array();
            foreach ($data['user_info_list'] as $u ) {
                if ($u['subscribe'] == 0) {
                    continue;
                }
                $uid = intval($uids[$u['openid']]);
                if ($uid == 0) {
                    // 新增加的用户
                    $rs = $this->weixin_model->add_user($u);
                } else {
                    // 更新的用户
                    $rs = $this->weixin_model->edit_user($uid, array(
                        'groupid' => (int)$u['groupid'],
                        'openid' => $u['openid'],
                        'nickname' => dr_deal_emoji($u['nickname'], 0),
                        'sex' => $u['sex'],
                        'city' => $u['city'],
                        'country' => $u['country'],
                        'province' => $u['province'],
                        'language' => $u['language'],
                        'headimgurl' => $u['headimgurl'],
                        'subscribe_time' => $u['subscribe_time'],
                    ));
                }
                // 更新关注表状态
                $this->db->where('openid', $u['openid'])->update($this->weixin_model->prefix.'_follow', array(
                    'status' => 1,
                    'uid' => $rs,
                ));
                $update[] = $u['openid'];
            }
            // 判断没获取到的
            foreach ($list as $t) {
                if (!in_array($t['openid'], $update)) {
                    $this->db->where('openid', $t['openid'])->update($this->weixin_model->prefix.'_follow', array(
                        'status' => 1,
                        'uid' => 0,
                    ));
                }
            }
            $this->admin_msg ( '同步用中，请勿关闭', dr_url('wuser/syc', array('action' =>'user')), 1, 0);
        }

    }
}