<?php


/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class M_Table extends M_Controller {

	public $myid; // 主键
	public $mydb; // 数据库
	public $tfield; // 时间字段
	public $mytable; // 表名称
	public $mywhere; // 查询条件
	public $myfield; // 全部字段
	public $mygroup; // 分组查询字段
	public $cache_file; // 缓存文件名
	public $post_param; // 提交返回参数数组

	/**
	 * 构造函数（自定义数据表管理）
	 */
	public function __construct() {
		parent::__construct();
		$this->myid = 'id';
		$this->load->library('Dfield');
		$this->cache_file = md5($this->duri->uri(1).$this->uid.$this->sid.$this->input->ip_address().$this->input->user_agent()); // 缓存文件名称
	}

	/**
	 * 条件查询
	 *
	 * @param	object	$select	查询对象
	 * @param	intval	$where	是否搜索
	 * @return	intval
	 */
	protected function _where(&$select, $param) {

		// 存在POST提交时
		if (IS_POST) {
			$search = $this->input->post('data');
			$param['keyword'] = $search['keyword'];
			$param['start'] = $search['start'];
			$param['end'] = $search['end'];
			$param['field'] = $search['field'];
		}

		// 条件查询
		$this->mywhere && $select->where($this->mywhere);
		$this->mygroup && $select->group_by($this->mygroup);
		
		// 存在search参数时，读取缓存文件
		if ($param) {
			$field = $this->myfield;
			!isset($field[$this->myid]) && $field[$this->myid] = 'id';
			if (isset($param['keyword']) && $param['keyword'] != '' && isset($field[$param['field']])) {
				$param['field'] = $param['field'] ? $param['field'] : 'subject';
				if ($param['field'] == 'id') {
					// 按id查询
					$id = array();
					$ids = explode(',', $param['keyword']);
					foreach ($ids as $i) {
						$id[] = (int)$i;
					}
					$select->where_in('id', $id);
				} elseif ($field[$param['field']]['fieldtype'] == 'Linkage'
					&& $field[$param['field']]['setting']['option']['linkage']) {
					// 联动菜单搜索
					if (is_numeric($param['keyword'])) {
						// 联动菜单id查询
						$link = dr_linkage($field[$param['field']]['setting']['option']['linkage'], (int)$param['keyword'], 0, 'childids');
						$link && $select->where($param['field'].' IN ('.$link.')');
					} else {
						// 联动菜单名称查询
						$id = (int)$this->ci->get_cache('linkid-'.SITE_ID, $field[$param['field']]['setting']['option']['linkage']);
						$id && $select->where($param['field'].' IN (select id from `'.$select->dbprefix('linkage_data_'.$id).'` where `name` like "%'.$param['keyword'].'%")');
					}
				} else {
					$select->like($param['field'], urldecode($param['keyword']));
				}
			}
			// 时间搜索
			if (isset($param['start']) && $param['start']) {
				$param['end'] = strtotime(date('Y-m-d 23:59:59', $param['end'] ? $param['end'] : SYS_TIME));
				$param['start'] = strtotime(date('Y-m-d 00:00:00', $param['start']));
				$select->where($this->tfield.' BETWEEN ' . $param['start'] . ' AND ' . $param['end']);
			} elseif (isset($param['end']) && $param['end']) {
				$param['end'] = strtotime(date('Y-m-d 23:59:59', $param['end']));
				$param['start'] = 1;
				$select->where($this->tfield.' BETWEEN ' . $param['start'] . ' AND ' . $param['end']);
			}
		}

		return $param;
	}

	/**
	 * 数据分页显示
	 *
	 * @return	array
	 */
	protected function limit_page() {

		if (IS_POST) {
			$page = $_GET['page'] = 1;
			$total = 0;
		} else {
			$page = max(1, (int)$this->input->get('page'));
			$total = (int)$this->input->get('total');
		}

		$param = $this->input->get(NULL);
		unset($param['s'],$param['c'],$param['m'],$param['d'],$param['page']);

		if (!$total) {
			$select	= $this->mydb->select('count(*) as total');
			$param = $this->_where($select, $param);
			if ($this->mygroup) {
				$total = count($select->get($this->mytable)->result_array());
			} else {
				$data = $select->get($this->mytable)->row_array();
				$total = (int)$data['total'];
			}
			unset($select);
			if (!$total) {
				return array(array(), $total, $param);
			}
		}

		$select	= $this->mydb->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
		$this->mygroup && $select->select('*,count('.$this->myid.') as count');
		$param = $this->_where($select, $param);
		$_order = isset($_GET['order']) && strpos($_GET['order'], 'undefined') !== 0 ? $this->input->get('order') : $this->tfield.' DESC';
		$data = $select->order_by($_order)->get($this->mytable)->result_array();

		return array($data, $total, $param);
	}

	/**
	 * 管理
	 */
	protected function _index($uri = '') {
		// 数据库中分页查询
		list($data, $total, $param)	= $this->limit_page();
		$param['total'] = $total;
		$this->template->assign(array(
			'list' => $data,
			'total' => $total,
			'pages'	=> $this->get_pagination(dr_url(APP_DIR.'/'.$this->router->class.'/'.$this->router->method, $param), $total),
			'param' => $param,
		));
		// 存储当前页URL
		$uri && $this->_set_back_url($uri, $param);
		return $data;
	}

	/**
	 * 修改
	 */
	protected function _edit() {

		$id = (int)$this->input->get($this->myid);
		$data = $this->_get_data($id);
		!$data && $this->admin_msg(fc_lang('当前数据不存在'));

		if (IS_POST) {
			$post = $this->validate_filter($this->myfield, $data);
			if (isset($post['error'])) {
				$error = $post;
				$data = $this->input->post('data', TRUE);
			} else {
				$this->_update_data($id, $post[1], $data);
				// 操作成功处理附件
				$this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable).'-'.$id, $this->myfield, $data);
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/index', $this->post_param), 1);
			}
		}

		$this->template->assign(array(
			'data' => $data,
			'error' => $error,
			'myfield' => $this->field_input($this->myfield, $data, TRUE)
		));

		return $data;
	}

	/**
	 * 添加
	 */
	protected function _add() {

		$data = array();

		if (IS_POST) {
			$post = $this->validate_filter($this->myfield);
			if (isset($post['error'])) {
				$error = $post;
				$data = $this->input->post('data', TRUE);
			} else {
				$id = $this->_insert_data($post[1]);
                if (!$id) {
                    $this->admin_msg('入库失败');
                }
				// 操作成功处理附件
				$this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable).'-'.$id, $this->myfield, $post);
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/index', $this->post_param), 1);
			}
		}

		$this->template->assign(array(
			'data' => $data,
			'error' => $error,
			'myfield' => $this->field_input($this->myfield, $data, TRUE)
		));

		return $data;
	}

	// 重写:获取数据
	public function _get_data($id) {
		return $this->mydb->where($this->myid, intval($id))->get($this->mytable)->row_array();
	}

	// 重写:更新数据
	public function _update_data($id, $data, $_data) {
		$this->mydb->where($this->myid, $id)->update($this->mytable, $data);
	}

	// 重写:插入数据
	public function _insert_data($data) {
		$this->mydb->insert($this->mytable, $data);
		return $this->mydb->insert_id();
	}

}