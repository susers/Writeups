<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

	
class Attachment extends M_Controller {

	private $cache_file;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$menu = array(
			fc_lang('附件管理') => array('admin/attachment/index', 'folder'),
		);
		defined('SYS_ATTACHMENT_DB') && (int)SYS_ATTACHMENT_DB && $menu[fc_lang('未使用的附件')] = array('admin/attachment/unused', 'folder-o');
		$this->template->assign('menu', $this->get_menu_v3($menu));
		$this->cache_file = md5('admin/attachment'.$this->uid.SITE_ID.$this->input->ip_address().$this->input->user_agent()); // 缓存文件名称
		$this->load->model('attachment_model');
    }
	
	/**
     * 搜索
     */
    public function index() {
		
		$error = 0;
		
		if (IS_POST) {
			$data = $this->input->post('data');
			$data['id'] = (int)$data['id'];
			if (!$data['author'] && !$data['ext']) {
				$error = fc_lang('必须填写其中一项搜索条件');
			} else {
				$where = array();

				if ($data['author']) {
					$uid = get_member_id($data['author']);
					if ($uid) {
						$where[] = '`uid`='.$uid;
					} else {
						$error = fc_lang('会员不存在');
						$where = NULL;
					}
				}
				if (!$error && $data['ext']) {
					$ext = explode(',', $data['ext']);
					$_ext = array();
					foreach ($ext as $t) {
						$_ext[] = '`fileext`="'.$t.'"';
					}
					$where[] = '('.implode(' OR ', $_ext).')';
				}
				if ($where) {
					$where = implode(' AND ', $where);
					$attach = $this->db->select('id')->where($where)->get('attachment')->result_array();
					if ($attach) {
						$cache = array();
						foreach ($attach as $t) {
							$cache[] = (int)$t['id'];
						}
						$this->cache->file->save($this->cache_file, $cache, 7200);
						$this->admin_msg(fc_lang('正在搜索中，请稍后...'), dr_url('attachment/result'), 2, 3);
					}
				}
				$error = fc_lang('没有搜索到相关附件，请检查搜索条件');
			}
			$data['id'] = $data['id'] ? $data['id'] : '';
		}
		
		$this->template->assign(array(
			'data' => $data,
			'error' => $error,
		));
		$this->template->display('attachment_index.html');
    }
	
	/**
     * 搜索结果
     */
	public function result() {
	
		if ($this->input->post('ids')) {
			$ids = $this->input->post('ids');
			$data = $this->db->where_in('id', $ids)->get('attachment')->result_array();
			if ($data) {
				foreach ($data as $t) {
					$this->db->delete('attachment', 'id='.$t['id']);
					$this->attachment_model->_delete_attachment($t);
                    $this->system_log('删除附件【#'.$t['id'].'】'); // 记录日志
				}
			}
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
	
		$cache = $this->cache->file->get($this->cache_file);
		$total = count($cache);
		if (!$total) {
            $this->admin_msg(fc_lang('搜索缓存已过期，请重新搜索'));
        }
		
		$page = max((int)$this->input->get('page'), 1);
		$data = $this->db
					 ->select('id,tableid')
					 ->where_in('id', $cache)
                     ->order_by('id desc')
					 ->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1))
					 ->get('attachment')
					 ->result_array();
		$result = array();
		foreach ($data as $i => $t) {
            $row = $this->db->where('id', (int)$t['id'])->get('attachment_'.(int)$t['tableid'])->row_array();
            if ($row) {
                $result[$i] = $row;
            }
		}

		$this->template->assign('menu', $this->get_menu_v3(array(
			fc_lang('搜索') => array('admin/attachment/index', 'search'),
			fc_lang('附件管理') => array('admin/attachment/result', 'folder'),
		)));
		$this->template->assign(array(
			'list' => $result,
			'pages'	=> $this->get_pagination(dr_url(APP_DIR.'/attachment/result'), $total),
            'totals' => $total,
		));
		$this->template->display('attachment_result.html');
	}

}