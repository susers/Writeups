<?php


/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */


class Db extends M_Controller {
	
	private $siteid;
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->siteid = (int)$this->input->get('siteid');
    }

    /**
     * 数据维护
     */
    public function index() {
	
		$list = $this->siteid ? $this->system_model->get_site_table($this->siteid) : $this->system_model->get_system_table();
		
		if (IS_POST) {
			$tables = $this->input->post('select');
			if (!$tables) {
                $this->admin_msg(fc_lang('貌似你还没有选择需要操作的表呢'));
            }
			switch ((int)$this->input->post('action')) {
				case 1: // 优化表
					foreach ($tables as $table) {
						$this->db->query("OPTIMIZE TABLE `$table`");
					}
					$result = fc_lang('操作成功，正在刷新...');
                    $this->system_log('优化数据表'); // 记录日志
					break;
				case 2: // 修复表
					foreach ($tables as $table) {
						$this->db->query("REPAIR TABLE `$table`");
					}
					$result = fc_lang('操作成功，正在刷新...');
                    $this->system_log('修复数据表'); // 记录日志
					break;
			}
		}
		
		$menu = array();
		$menu[fc_lang('系统数据库')] = array('admin/db/index', 'database');
		foreach ($this->site_info as $id => $s) {
			$menu[fc_lang('站点【#%s】', $id)] = array('admin/db/index/siteid/'.$id, 'database');
		}

		$this->template->assign(array(
			'menu' => $this->get_menu_v3($menu),
			'list' => $list,
			'result' => $result,
		));
		$this->template->display('db_index.html');
	}

	/**
     * 表结构
     */
    public function tableshow() {
		$name = $this->input->get('name');
		$cache = $this->get_cache('table');
		$this->template->assign('table', $cache[$name]);
		$this->template->display('db_table.html');
	}

}