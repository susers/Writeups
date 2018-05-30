<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

	
class Block extends M_Controller {

	private $field;
	private $tablename;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

		$this->tablename = $this->db->dbprefix(SITE_ID.'_block');
		$this->field = array(
			'name' => array(
				'ismain' => 1,
				'fieldname' => 'name',
				'fieldtype' => 'Text',
				'setting' => array(
					'option' => array(
						'width' => 200,
					),
					'validate' => array(
						'required' => 1,
					)
				)
			),
			'value_1' => array(
				'ismain' => 1,
				'fieldname' => 'value_1',
				'fieldtype'	=> 'Textarea',
				'setting' => array(
					'option' => array(
						'width' => '90%',
						'height' => 250,
					),
                    'validate' => array(
                        'xss' => 1,
                    )
				)
			),
			'value_2' => array(
				'ismain' => 1,
				'fieldtype' => 'Ueditor',
				'fieldname' => 'value_2',
				'setting' => array(
					'option' => array(
						'mode' => 1,
						'height' => 300,
						'width' => '100%'
					)
				)
			),
			'value_3' => array(
				'ismain' => 1,
				'fieldtype' => 'File',
				'fieldname' => 'value_3',
				'setting' => array(
					'option' => array(
						'ext' => '*',
						'size' => 99999,
					)
				)
			),
			'value_4' => array(
				'ismain' => 1,
				'fieldtype' => 'Files',
				'fieldname' => 'value_4',
				'setting' => array(
					'option' => array(
						'ext' => '*',
						'size' => 99999,
						'count' => 99999,
					)
				)
			),
		);
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('自定义内容') => array('admin/block/index', 'file'),
				fc_lang('添加') => array('admin/block/add', 'plus'),
			)),
			'type' => array(
				'1' => fc_lang('纯文本'),
				'2' => fc_lang('富文本'),
				'3' => fc_lang('单文件'),
				'4' => fc_lang('多文件'),
			)
		));
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
            $this->db->where_in('id', $ids)->delete($this->tablename);
            $this->system_log('删除自定义页面【#'.@implode(',', $ids).'】'); // 记录日志
            $this->cache(1);
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
		
		$this->template->assign('list', $this->db->get($this->tablename)->result_array());
		$this->template->display('block_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		if (IS_POST) {
			$post = $this->validate_filter($this->field);
			// 格式化内容
			$data = array(
				'name' => $post[1]['name'] ? $post[1]['name'] : fc_lang('未命名'),
			);
			switch (intval($_POST['type'])) {
				case 1:
					// 文本内容
					$data['content'] = $post[1]['value_1'] ? $post[1]['value_1'] : '';
					break;
				case 2:
					// 丰富文本
					$data['content'] = '{i-2}:'.($post[1]['value_2'] ? $post[1]['value_2'] : '');
					break;
				case 3:
					// 单文件
					$data['content'] = '{i-3}:'.($post[1]['value_3'] ? $post[1]['value_3'] : '');
					break;
				case 4:
					// 多文件
					$data['content'] = '{i-4}:'.($post[1]['value_4'] ? dr_array2string($post[1]['value_4']) : '');
					break;
			}
			$this->db->insert($this->tablename, $data);
            $id = $this->db->insert_id();
			$this->attachment_handle($this->uid, $this->tablename.'-'.$id, $this->field);
			$this->cache(1);
            $this->system_log('添加自定义页面【'.$data['name'].'#'.$id.'】'); // 记录日志
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('block/index'), 1);
		}
		
		$this->template->assign(array(
			'field' => $this->field,
        ));
		$this->template->display('block_add.html');
    }

	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->limit(1)->get($this->tablename)->row_array();
		!$data && $this->admin_msg(fc_lang('数据不存在'));

		$data = dr_get_block_value($data);
		
		if (IS_POST) {
			$post = $this->validate_filter($this->field);
			// 格式化内容
			$data = array(
				'name' => $post[1]['name'] ? $post[1]['name'] : fc_lang('未命名'),
			);
			switch (intval($_POST['type'])) {
				case 1:
					// 文本内容
					$data['content'] = $post[1]['value_1'] ? $post[1]['value_1'] : '';
					break;
				case 2:
					// 丰富文本
					$data['content'] = '{i-2}:'.($post[1]['value_2'] ? $post[1]['value_2'] : '');
					break;
				case 3:
					// 单文件
					$data['content'] = '{i-3}:'.($post[1]['value_3'] ? $post[1]['value_3'] : '');
					break;
				case 4:
					// 多文件
					$data['content'] = '{i-4}:'.($post[1]['value_4'] ? dr_array2string($post[1]['value_4']) : '');
					break;
			}
			$this->db->where('id',(int)$id)->update($this->tablename, $data);
			$this->attachment_handle($this->uid, $this->tablename.'-'.$id, $this->field, $post);
			$this->cache(1);
            $this->system_log('修改自定义页面【'.$data['name'].'#'.$id.'】'); // 记录日志
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('block/index'), 1);
		}

		$this->template->assign(array(
			'i' => $data['i'],
			'data' => $data,
			'field' => $this->field,
        ));
		$this->template->display('block_add.html');
    }
	
    /**
     * 缓存
     */
    public function cache($update = 0) {
		$this->system_model->block(isset($_GET['site']) && $_GET['site'] ? (int)$_GET['site'] : SITE_ID);
		((int)$_GET['admin'] || $update) or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}