<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class linkage extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->load->model('linkage_model');
    }
	
	/**
     * index
     */
    public function index() {

		if (IS_POST) {
			$ids = $this->input->post('ids', TRUE);
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            } elseif (!$this->is_auth('admin/linkage/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
			$this->db->where_in('id', $ids)->delete('linkage');
			foreach ($ids as $key) {
				$this->db->query('DROP TABLE `'.$this->db->dbprefix('linkage_data_'.$key).'`');
                $this->system_log('删除联动菜单【#'.$key.'】'); // 记录日志
			}
			exit(dr_json(1, fc_lang('操作成功')));
		}

		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('联动菜单') => array(APP_DIR.'/admin/linkage/index', 'windows'),
				fc_lang('添加') => array(APP_DIR.'/admin/linkage/add_js', 'plus'),
				fc_lang('更新缓存') => array(APP_DIR.'/admin/linkage/cache', 'refresh'),
			)),
			'list' => $this->linkage_model->get_data(),
            'dt_data' => array(
                1 => '省级',
                2 => '省市',
                3 => '省市县',
            ),
		));
		$this->template->display('linkage_index.html');
	}
	
	/**
     * 添加
     */
    public function add() {

		if (IS_POST) {
            $result = $this->linkage_model->add($this->input->post('data'));
			if (is_array($result)) {
                exit(dr_json(0, $result['error'], $result['name']));
            } else {
                $this->system_log('添加联动菜单【#'.$result.'】'); // 记录日志
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            }
		}

		$this->template->display('linkage_add.html');
    }
	
	/**
     * 修改
     */
    public function edit() {

		$id = (int)$this->input->get('id');
		$data = $this->linkage_model->get($id);
		if (!$data)	{
            exit(fc_lang('对不起，数据被删除或者查询不存在'));
        }

		if (IS_POST) {
			$result	= $this->linkage_model->edit($id, $this->input->post('data'));
            if (is_array($result)) {
                exit(dr_json(0, $result['error'], $result['name']));
            } else {
                $this->system_log('修改联动菜单【#'.$id.'】'); // 记录日志
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            }
		}

		$this->template->assign(array(
			'data' => $data,
		));
		$this->template->display('linkage_add.html');
	}
	
    /**
     * 菜单
     */
    public function data() {

		$key = (int)$this->input->get('key');
		$pid = (int)$this->input->get('pid');
		$link = $this->linkage_model->get($key);
		if (!$link)	{
            $this->admin_msg(fc_lang('联动菜单不存在!'));
        }

		if (IS_POST) {
			$ids = $this->input->post('ids', TRUE);
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
			if ($this->input->post('action') == 'order') {
				$data = $this->input->post('data');
				foreach ($ids as $id) {
					$this->db->where('id', (int)$id)->update('linkage_data_'.$key, $data[$id]);
				}
                $this->system_log('排序联动菜单【#'.$key.'】数据值【#'.@implode(',', $ids).'】'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功')));
			} elseif ($this->input->post('action') == 'move') {
                $pid = (int)$this->input->post('pid');
                foreach ($ids as $id) {
                    $this->db->where('id', (int)$id)->update('linkage_data_'.$key, array('pid' => $pid));
                }
                $this->system_log('移动分类联动菜单【#'.$key.'】数据值【#'.@implode(',', $ids).'】'); // 记录日志
                exit(dr_json(1, fc_lang('操作成功')));
            } else {
				if (!$this->is_auth(APP_DIR.'/admin/linkage/del')) {
                    exit(dr_json(0, fc_lang('您无权限操作')));
                }
				$delete = '';
				foreach ($ids as $id) {
					$data = $this->db->where('id', $id)->get('linkage_data_'.$key)->row_array();
					if ($data['childids']) {
                        $delete.= $data['childids'].',';
                    }
				}
				$delete = trim($delete, ',');
				if ($delete) {
					$this->db->query("delete from {$this->db->dbprefix('linkage_data_'.$key)} where id in ($delete)");
					$this->linkage_model->repair($key);
				}
                $this->system_log('删联动菜单【#'.$key.'】数据值【#'.@implode(',', $ids).'】'); // 记录日志
                exit(dr_json(1, fc_lang('操作成功')));
			}
		}
		$this->template->assign(array(
			'key' => $key,
			'pid' => $pid,
			'list' => $this->linkage_model->get_list_data($link, $pid),
			'menu' => $this->get_menu_v3(array(
				lang('返回') => array(APP_DIR.'/admin/linkage/index', 'reply'),
				fc_lang('联动菜单') => array(APP_DIR.'/admin/linkage/data/key/'.$key, 'windows'),
				fc_lang('添加') => array(APP_DIR.'/admin/linkage/adds/key/'.$key.'_js', 'plus'),
			)),
            'select' => $this->select_linkage($this->linkage_model->get_list_data($link), 0, 'name=\'pid\'', fc_lang('顶级菜单')),
		));
		$this->template->display('linkage_data.html');
    }
	
	/**
     * 添加
     */
    public function adds() {

		$pid = (int)$this->input->get('pid');
		$key = (int)$this->input->get('key');
		$link = $this->linkage_model->get($key);
		if (!$link)	{
            exit(fc_lang('联动菜单不存在!'));
        }

		if (IS_POST) {
			$result	= $this->linkage_model->adds($key, $this->input->post('data'));
            if (is_array($result)) {
                exit(dr_json(0, $result['error'], $result['name']));
            } else {
                $this->system_log('添加联动菜单【#'.$key.'】数据值'); // 记录日志
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            }
		}
		$this->template->assign(array(
			'select' => $this->select_linkage($this->linkage_model->get_list_data($link), $pid, 'name=\'data[pid]\'', fc_lang('顶级菜单')),
		));
		$this->template->display('linkage_adds.html');
	}
	
	/**
     * 修改
     */
    public function edits() {

		$id = (int)$this->input->get('id');
		$key = (int)$this->input->get('key');
		$link = $this->linkage_model->get($key);
		if (!$link)	{
            exit(fc_lang('联动菜单不存在!'));
        }
		$data = $this->linkage_model->gets($id, $key);
		if (!$data)	{
            exit(fc_lang('对不起，数据被删除或者查询不存在'));
        }

		if (IS_POST) {
			$edit = $this->input->post('data');
			$edit['pid'] = $edit['pid'] == $id ? $data['pid'] : $edit['pid'];
			$result	= $this->linkage_model->edits($key, $id, $edit);
            if (is_array($result)) {
                exit(dr_json(0, $result['error'], $result['name']));
            } else {
                $this->system_log('修改联动菜单【#'.$key.'】数据值【#'.$id.'】'); // 记录日志
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            }
			exit;
		}

		$this->template->assign(array(
			'data' => $data,
			'select' => $this->select_linkage($this->linkage_model->get_list_data($link), $data['pid'], 'name=\'data[pid]\'', fc_lang('顶级菜单')),
		));
		$this->template->display('linkage_adds.html');
	}

	// 设置隐藏
	public function hidden() {

		$id = (int)$this->input->get('id');
		$key = (int)$this->input->get('key');
		$link = $this->linkage_model->get($key);
		if (!$link)	{
			exit(fc_lang('联动菜单不存在!'));
		}
		$data = $this->linkage_model->gets($id, $key);
		if (!$data)	{
			exit(fc_lang('对不起，数据被删除或者查询不存在'));
		}

		$this->db->where('id IN ('.$data['childids'].')')->update('linkage_data_'.$key, array(
			'hidden' => $data['hidden'] ? 0 : 1,
		));
		$this->system_log('更新联动菜单【#'.$key.'-'.$data['name'].'】显示状态'); // 记录日志
		$this->admin_msg(fc_lang('操作成功'), dr_url('linkage/data', array('key'=>$key)), 1);
	}
	
	/**
     * 导入联动数据
     */
    public function import() {
		if ($this->input->get('admin')) {

            $id = (int)$_GET['id'];
            $lid = (int)$_GET['lid'];
            !is_file(FCPATH.'dayrui/libraries/Linkage/'.$id.'.php') && $this->admin_msg(fc_lang('数据文件不存在无法导入'));

            // 清空数据
            $table = 'linkage_data_'.$lid;
            $this->db->query('TRUNCATE `'.$this->db->dbprefix($table).'`');
            $count = 0;

            // 开始导入
            $data = require FCPATH.'dayrui/libraries/Linkage/'.$id.'.php';
            foreach ($data as $t) {
                $rt = $this->db->insert($table, $t);
                if ($rt) {
                    $count++;
                }
            }

			$this->admin_msg(fc_lang('共%s条数据，导入成功%s条', count($data), $count), dr_url('linkage/index'), 1);
		} else {
			$this->admin_msg('导入中 ... ', dr_url('linkage/import', array('admin' => 1, 'id' => $_GET['id'], 'lid' => $_GET['lid'])), 2, 0);
		}
	}
	
	/**
     * 缓存
     */
    public function cache() {
		$this->linkage_model->cache(isset($_GET['site']) && $_GET['site'] ? (int)$_GET['site'] : SITE_ID);
		(int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
	
	/**
	 * 栏目选择
	 *
	 * @param array			$data		栏目数据
	 * @param intval/array	$id			被选中的ID，多选是可以是数组
	 * @param string		$str		属性
	 * @param string		$default	默认选项
	 * @return string
	 */
	public function select_linkage($data, $id = 0, $str = '', $default = ' -- ') {
		$tree = array();
		$string = '<select class="form-control" '.$str.'>';
		if ($default) {
            $string.= "<option value='0'>$default</option>";
        }
		if (is_array($data)) {
			foreach($data as $t) {
				// 选中操作
				$t['selected'] = '';
				if (is_array($id)) {
					$t['selected'] = in_array($t['id'], $id) ? 'selected' : '';
				} elseif(is_numeric($id)) {
					$t['selected'] = $id == $t['id'] ? 'selected' : '';
				}
				$tree[$t['id']] = $t;
			}
		}
		$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
		$str2 = "<optgroup label='\$spacer \$name'></optgroup>";
		$this->load->library('dtree');
		$this->dtree->init($tree);
		$string.= $this->dtree->get_tree_category(0, $str, $str2);
		$string.= '</select>';
		return $string;
	}
}