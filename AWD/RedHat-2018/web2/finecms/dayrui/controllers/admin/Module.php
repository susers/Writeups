<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Module extends M_Controller {
	
	private $_menu;
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->_menu = array(
			fc_lang('模型管理') => array('admin/module/index', 'cogs'),
		);
		$this->template->assign(array(
			'menu' => $this->get_menu_v3($this->_menu),
			'duri' => $this->duri
		));
		$this->load->model('module_model');
    }

    /**
     * 模型
     */
    public function index() {

		$this->template->assign(array(
			'list' => $this->module_model->get_data(),
		));
		$this->template->display('module_index.html');
	}
	
	/**
     * 配置
     */
    public function config() {

        $id = $this->input->get('id');
        $data = $this->module_model->get($id);
        if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

        if (IS_POST) {
            $post = $this->input->post('site');
            $post['use'] = 1;
            $data['site'] = $post;
            $this->db->where('id', $id)->update('module', array(
                'site' => dr_array2string($data['site']),
            ));
            $this->system_log('配置模型【'.$data['dirname'].'】'); // 记录日志
            $this->clear_cache('module');
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('module/index', array('id' => $id)), 1);
        }


        $this->template->assign(array(
            'data' => $data,
            'theme' => dr_get_theme(),
            'is_theme' => strpos($data['site']['theme'], 'http://') === 0 ? 1 : 0,
            'template_path' => dr_dir_map(TPLPATH.'pc/web/', 1),
        ));
        $this->template->display('module_config.html');
    }

	/**
     * 禁用/可用
     */
    public function disabled() {
        $id = (int)$this->input->get('id');
        $_data = $this->db->where('id', $id)->get('module')->row_array();
        $value = $_data['disabled'] == 1 ? 0 : 1;
        $this->db->where('id', $id)->update('module', array('disabled' => $value));
        $this->system_log(($value ? '禁用' : '启用').'模型【'.$_data['dirname'].'】'); // 记录日志
        $this->clear_cache('module');
    }
	
	/**
     * 创建
     */
    public function add() {

        if (IS_POST) {

            $data = $this->input->post('data');
            if (!$data['dirname'] || !preg_match('/^[a-z]+$/U', $data['dirname'])) {
                exit(dr_json(0, fc_lang('模型目录格式不正确，只能由英文字母组成')));
            } elseif ($data['name'] && strpos($data['name'], "'") !== FALSE) {
                exit(dr_json(0, fc_lang('名称不不规范')));
            }

            $rt = $this->module_model->add($data['name'], $data['dirname']);
            if ($rt) {
                exit(dr_json(0, fc_lang($rt)));
            }

            $this->system_log('创建模型'.$data['dirname'].'】'); // 记录日志
            exit(dr_json(1, fc_lang('模型创建成功')));
        } else {
            $this->template->display('module_add.html');
        }
    }


	/**
     * 卸载
     */
    public function uninstall() {
        $id = $this->input->get('id');
        $this->module_model->del((int)$id);
        $this->system_log('卸载模型【#'.$id.'】'); // 记录日志
        $this->clear_cache('module');
        $this->admin_msg(fc_lang('全部站点卸载成功（请更新模型缓存）'), dr_url('module/index'), 1);
    }

	
	/**
     * 缓存
	 *
     */
    public function cache() {
		$this->module_model->cache();
    }
	

}