<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */
	
class Form extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->load->model('form_model');
    }
	
	/**
     * 管理
     */
    public function index() {
		$this->template->assign(array(
			'list' => $this->db->get($this->form_model->prefix)->result_array(),
			'menu' => $this->get_menu_v3(array(
				fc_lang('表单管理') => array('admin/form/index', 'table'),
				fc_lang('添加') => array('admin/form/add', 'plus')
			)),
		));
		$this->template->display('form_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		if (IS_POST) {
			$data = $this->input->post('data');
			$result = $this->form_model->add($data);
			if ($result === TRUE) {
				$this->form_model->cache();
                $this->system_log('添加网站表单【#'.$data['table'].'】'); // 记录日志
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('form/index'), 1);
			}
		}
		
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('表单管理') => array('admin/form/index', 'table'),
				fc_lang('添加') => array('admin/form/add', 'plus'),
				fc_lang('更新缓存') => array('admin/form/cache', 'refresh'),
			)),
			'data' => $data,
			'result' => $result,
		));
		$this->template->display('form_add.html');
    }
	
	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->limit(1)->get($this->form_model->prefix)->row_array();
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }
		
		if (IS_POST) {
            $this->system_log('修改网站表单【#'.$data['table'].'】'); // 记录日志
			$data = $this->input->post('data');
			$this->form_model->edit($id, $data);
			$this->form_model->cache();
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('form/index'), 1);
		}
		
		$data['setting'] = dr_string2array($data['setting']);
		
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('表单管理') => array('admin/form/index', 'table'),
				fc_lang('添加') => array('admin/form/add', 'plus'),
				fc_lang('更新缓存') => array('admin/form/cache', 'refresh'),
			)),
			'data' => $data,
		));
		$this->template->display('form_add.html');
    }
	
	/**
     * 删除
     */
    public function del() {
        $id = (int)$this->input->get('id');
		$this->form_model->del($id);
        $this->system_log('删除网站表单【#'.$id.'】'); // 记录日志
		$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('form/index'), 1);
	}
	

	/**
     * 缓存
     */
    public function cache() {
		$this->form_model->cache($site = isset($_GET['site']) && $_GET['site'] ? (int)$_GET['site'] : SITE_ID);
        (int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
	
}