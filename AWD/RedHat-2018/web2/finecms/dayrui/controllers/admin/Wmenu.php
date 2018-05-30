<?php
/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

require FCPATH.'dayrui/core/M_Table.php';

class Wmenu extends M_Table {


    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('weixin_model');
        $this->mydb = $this->db; // 数据库
        $this->tfield = 'displayorder'; // 时间字段用于搜索和排序
        $this->mytable = $this->weixin_model->prefix . '_menu';

        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                '菜单管理' => array('admin/wmenu/index', 'table'),
                '添加' => array('admin/wmenu/add', 'plus'),
                '同步到微信服务器' => array('admin/wmenu/syc', 'refresh'),
            )),
            'type' => array(
                'url' => 'URL链接',
                'member' => '登录授权',
            ),
        ));
    }


    // 同步到微信
    public function syc() {

        $data = $this->db->where('pid', 0)->order_by('displayorder asc')->get($this->mytable)->result_array();
        if ($data) {
            $json = array();
            foreach ($data as $i => $t) {
                $list = $this->db->where('pid', (int)$t['id'])->order_by('displayorder asc')->get($this->mytable)->result_array();
                if ($list) {
                    $val = array();
                    foreach ($list as $c) {
                        $val[] = $this->_get_menu_data($c);
                    }
                    $json[] = array(
                        'name' => $t['name'],
                        'sub_button' => $val
                    );
                } else {
                    $json[] = $this->_get_menu_data($t);
                }
            }

            $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . dr_get_access_token ();
            $res = dr_weixin_post( $url, dr_weixin_en_json(array('button'=>$json)));
            if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
                $this->admin_msg(dr_error_msg ( $res, '' ) );
            }else{
                $this->admin_msg('同步成功，需要24小时微信客户端才会展现出来。', '', 1);
            }
        } else {
            $this->admin_msg('你还没有创建菜单呢！');
        }
    }

    // 获取同步规则值
    private function _get_menu_data($data) {

        // 按应用来判断类别
        if ($data['type'] == 'url') {
            // 地址模式
            return array(
                'type' => 'view',
                'name' => $data['name'],
                'url' => $data['value'],
            );
        } elseif ($data['type'] == 'member') {
            // 授权模式
            return array(
                'type' => 'view',
                'name' => $data['name'],
                'url' => SITE_URL.'index.php?c=weixin&m=sync&url='.urlencode($data['value']),
            );
        }

    }

    /**
     * 菜单管理
     */
    public function index() {

        if (IS_POST) {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            // 可以不用判断权限
            if ($this->input->post('action') == 'order') {
                $post = $this->input->post('data');
                foreach ($ids as $id) {
                    $this->db->where('id', (int)$id)->update($this->mytable,  array('displayorder' => (int)$post[$id]['displayorder']));
                }
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            } else {
                foreach ($ids as $id) {
                    $this->db->where('id', (int)$id)->delete($this->mytable);
                    $this->db->where('pid', (int)$id)->delete($this->mytable);
                }
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            }
        }

        $data = $this->db->where('pid', 0)->order_by('displayorder asc')->get($this->mytable)->result_array();
        if ($data) {
            foreach ($data as $i => $t) {
                $data[$i]['data'] = $this->db
                    ->where('pid', (int)$t['id'])
                    ->order_by('displayorder asc')
                    ->get($this->mytable)
                    ->result_array();
            }
        }
        $this->template->assign(array(
            'list' => $data,
        ));
        $this->template->display('wmenu_index.html');
    }

    public function add() {

        $pid = (int)$this->input->get('pid');

        if (IS_POST) {
            $data = $this->input->post('data');
            $value = $this->input->post('value');
            if (!$data['name']) {
                $this->admin_msg('名称不能为空！');
            }
            $data['displayorder'] = 0;
            $data['value'] = isset($value[$data['type']]) ? $value[$data['type']] : '';
            $this->db->insert($this->mytable, $data);
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url($this->router->class.'/index'), 1);
        } else {
            $data['type'] = 'url';
        }

        $this->template->assign(array(
            'pid' => $pid,
            'top' => $this->db->where('pid', 0)->order_by('displayorder asc')->get($this->mytable)->result_array(),
            'data' => $data,
        ));
        $this->template->display('wmenu_add.html');
    }

    public function edit() {

        $id = (int)$this->input->get('id');
        $data = $this->db->where('id', $id)->get($this->mytable)->row_array();

        if (IS_POST) {
            $post = $this->input->post('data');
            if (!$post['name']) {
                $this->admin_msg('名称不能为空！');
            }
            $value = $this->input->post('value');
            $post['value'] = isset($value[$post['type']]) ? $value[$post['type']] : '';
            $this->db->where('id', $id)->update($this->mytable, $post);
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url($this->router->class.'/index'), 1);
        }

        $this->template->assign(array(
            'pid' => $data['pid'],
            'top' => $this->db->where('pid', 0)->order_by('displayorder asc')->get($this->mytable)->result_array(),
            'data' => $data,
        ));
        $this->template->display('wmenu_add.html');
    }


}