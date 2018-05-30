<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Urlrule extends M_Controller {

	private $type;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->type = array(
			3 => fc_lang('栏目'),
			4 => fc_lang('站点'),
		);
		$this->template->assign('type', $this->type);
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('URL规则') => array('admin/urlrule/index', 'magnet'),
		    fc_lang('添加') => array('admin/urlrule/add', 'plus'),
		    fc_lang('伪静态规则') => array('admin/route/index', 'safari'),
		)));
    }
	
	/**
     * 管理
     */
    public function index() {

		if (IS_POST) {
			$ids = $this->input->post('ids', TRUE);
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
			if (!$this->is_auth('admin/urlrule/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
            $this->db->where_in('id', $ids)->delete('urlrule');
			$this->cache(1);
            $this->system_log('删除URL规则【#'.@implode(',', $ids).'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}

		$this->template->assign(array(
			'list' => $this->db->get('urlrule')->result_array(),
			'color' => array(
				0 => 'default',
				1 => 'info',
				2 => 'success',
				3 => 'warning',
				4 => 'danger',
				5 => '',
				6 => 'primary',
			),
		));
		$this->template->display('urlrule_index.html');
    }

    /**
     * 复制
     */
    public function copy() {

        $id = (int)$this->input->get('id');
        $data = $this->db
                     ->where('id', $id)
                     ->limit(1)
                     ->get('urlrule')
                     ->row_array();
        if ($data) {
            $this->db->insert('urlrule', array(
                'type' => $data['type'],
                'name' => $data['name'].'_copy',
                'value' => $data['value'],
            ));
            $this->cache(1);
            $this->system_log('复制URL规则【#'.$id.'】'); // 记录日志
        }

        exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
    }
	
	/**
     * 添加
     */
    public function add() {

		if (IS_POST) {
			$this->db->insert('urlrule', array(
				'type' => $this->input->post('type'),
				'name' => $this->input->post('name'),
				'value' => dr_array2string($this->input->post('data')),
			 ));
            $this->system_log('添加URL规则【#'.$this->db->insert_id().'】'.$this->input->post('name')); // 记录日志
            $this->cache(1);
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('urlrule/index'), 1);
		}

		$this->template->display('urlrule_add.html');
    }

	/**
     * 修改
     */
    public function edit() {

		$id = (int)$this->input->get('id');
		$data = $this->db
					 ->where('id', $id)
					 ->limit(1)
					 ->get('urlrule')
					 ->row_array();
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

		if (IS_POST) {
			$this->db->where('id', $id)->update('urlrule', array(
				'name' => $this->input->post('name'),
				'value' => dr_array2string($this->input->post('data')),
			 ));
			$this->cache(1);
            $this->system_log('修改URL规则【#'.$id.'】'.$this->input->post('name')); // 记录
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('urlrule/index'), 1);
		}

		$data['value'] = dr_string2array($data['value']);
		$this->template->assign(array(
			'data' => $data,
        ));
		$this->template->display('urlrule_add.html');
    }
	
    /**
     * 缓存
     */
    public function cache($update = 0) {
		$this->system_model->urlrule();
		((int)$_GET['admin'] || $update) or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}