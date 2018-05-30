<?php

class Field extends M_Controller {

	public $name;
	public $data;
    public $func;

	public $backuri;
	public $cacheuri;
	public $relatedid;
	public $relatedname;

    /**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
        $this->load->model('field_model');
        $ismain = $issearch = $iscategory = 0;
		$this->relatedid = (int)$this->input->get('rid');
		$this->relatedname = $this->input->get('rname'); // 字段来源相关表
        if (strpos($this->relatedname, 'form') === 0) {
            // 网站表单字段
            $this->data = $this->get_cache($this->relatedname, $this->relatedid);
            $this->name = '网站表单【'.$this->data['table'].'】字段';
            $this->backuri = 'admin/form/index'; // 返回uri地址
            $this->cacheuri = $this->relatedname; // 缓存文件标示名称
            if (!$this->data) {
                $this->admin_msg(fc_lang('表单不存在，请更新表单缓存'));
            }
            $this->func = 'form';
        } elseif (strpos($this->relatedname, 'category') === 0) {
            // 模型栏目表字段
            $ismain = 1;
            // 表示共享栏目
            $this->name = '栏目字段';
            $this->backuri = 'admin/category/index'; // 返回uri地址
            $this->cacheuri = 'category_share'; // 缓存文件标示名称
            $this->data['dirname'] = 'share';
            $this->func = 'category_info';
        } elseif ($this->relatedname == 'module') {
            // 模型字段
            $this->load->model('module_model');
            $this->data = $this->module_model->get($this->relatedid);
            $this->backuri = 'admin/module/index'; // 返回uri地址
            $this->cacheuri = 'module'; // 缓存文件标示名称
            $this->name = '模型【'.$this->data['dirname'].'】字段';
            $this->func = 'module';
        } elseif ($this->relatedname == 'member') {
            // 会员字段
            $ismain = 1;
            $this->name = '会员字段';
            $this->backuri = 'member/admin/home/index'; // 返回uri地址
            $this->cacheuri = 'member'; // 缓存文件标示名称
            $this->func = 'member';
        }
        // load
		$this->load->library('Dfield', array($this->data['dirname']));
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				lang('返回') => array($this->backuri, 'reply'),
                $this->name ? $this->name : fc_lang('字段管理') => array('admin/field/index/rname/'.$this->relatedname.'/rid/'.$this->relatedid, 'cube'),
				fc_lang('添加') => array('admin/field/add/rname/'.$this->relatedname.'/rid/'.$this->relatedid,'plus')
			)),
			'rid' => $this->relatedid,
			'rname' => $this->relatedname,
			'module' => $this->data['dirname'],
			'ismain' => $ismain,
			'issearch' => $issearch,
			'iscategory' => $iscategory,
		));
    }
	
	/**
     * 管理
     */
    public function index() {

		if (IS_POST) {
			if ($this->input->post('action') == 'del') {
                $ids = $this->input->post('ids');
				$this->field_model->del($ids);
                $this->system_log('删除'.$this->name.'【#'.@implode(',', $ids).'】'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功')));
			} else {
				$_ids = $this->input->post('ids');
				$_data = $this->input->post('data');
				foreach ($_ids as $id) {
					$this->db->where('id', $id)->update('field', $_data[$id]);
				}
                $this->system_log('修改排序'.$this->name.'【#'.@implode(',', $_ids).'】'); // 记录日志
				unset($_ids, $_data);
				exit(dr_json(1, fc_lang('操作成功')));
			}
		}

		$data = $this->field_model->get_data();
		$group = array();
		if ($data) {
			foreach ($data as $t) {
				if (($t['fieldtype'] == 'Group' || $t['fieldtype'] == 'Merge') 
					&& preg_match_all('/\{(.+)\}/U', $t['setting']['option']['value'], $value)) {
					$group[$t['fieldname']] = dr_random_color();
					foreach ($value[1] as $v) {
						$group[$v] = $group[$t['fieldname']];
					}
				}
			}
		}

		$this->template->assign(array(
			'list' => $data,
			'group' => $group
		));
		$this->template->display('field_index.html');
	}
	
	/**
     * 添加
     */
    public function add() {

		// 初始化部分值
		$page = max((int)$this->input->post('page'), 0);
		$result	= $code = $data['fieldtype'] = $data['setting']['option'] = '';
		$data['setting']['validate']['required'] = $id = 0;
		// 可用字段类别
		$ftype = $this->dfield->type($this->module);
		
		if ($this->relatedname != 'module' 
			&& strpos($this->relatedname, 'extend') === false) {
			// 非模型和扩展字段时不显示的类别
			foreach ($ftype as $i => $t) {
				if (in_array($t['id'], array('Fees', 'Syn', 'Price'))) {
					unset($ftype[$i]);
				}
			}
		}
		if ($this->relatedname != 'module') {
			// 非模型字段时不显示的类别
			foreach ($ftype as $i => $t) {
				if (in_array($t['id'], array(
                    'Price',
                    'Syn',
                    'Mycategory',
                    'Property2',
                    'Shipping',
                    'Shipping_param',
                    'Specification',
                ))) {
					unset($ftype[$i]);
				}
			}
		}
		// 提交表单
		if (IS_POST) {
			$data = $this->input->post('data');
			$field = $this->dfield->get($data['fieldtype']);
			if (!$field) {
				$page = 0;
				$result	= fc_lang('字段类别不存在');
			} elseif (empty($data['name'])) {
				$page = 0;
				$code = 'name';
			} elseif (!preg_match('/^[a-z]+[a-z0-9_\-]+$/i', $data['fieldname'])) {
				$page = 0;
				$code = 'fieldname';
                $result	= fc_lang('字段名称不规范');
			} elseif (strlen($data['fieldname']) > 15) {
				$page = 0;
				$code = 'fieldname';
                $result	= fc_lang('字段名称太长');
			} elseif ($this->field_model->exitsts($data['fieldname'])) {
				$page = 0;
				$code = 'fieldname';
				$result	= fc_lang('字段已经存在');
			} else {
                $this->clear_cache($this->cacheuri);
                $sql = $field->create_sql($data['fieldname'], $data['setting']['option']);
                $this->field_model->add($data, $sql);
                $this->system_log('添加'.$this->name.'【'.$data['fieldname'].'】'); // 记录日志
                $this->admin_msg(fc_lang('操作成功'), dr_url('field/index', array('rname' => $this->relatedname, 'rid' => $this->relatedid)), 1);

			}

		}
        
		$this->template->assign(array(
			'id' => $id,
			'page' => $page,
			'code' => $code,
			'data' => $data,
            'role' => $this->get_cache('role'),
			'ftype' => $ftype,
			'result' => $result,
			'relatedid' => $this->relatedid,
			'relatedname' => $this->relatedname,
		));
		$this->template->display('field_add.html');
	}
	
	/**
     * 修改
     */
    public function edit() {

		$id = (int)$this->input->get('id');
		$data = $this->field_model->get($id);
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

		$page = (int)$this->input->post('page');
		$ftype = $this->dfield->type();
		$result	= $code = '';

		if (IS_POST) {
			$_data = $data;
			$data = $this->input->post('data');
			$field = $this->dfield->get($_data['fieldtype']);
			if (!$field) {
				$page = 0;
				$result	= fc_lang('字段类别不存在');
			} elseif (!$data['name']) {
				$page = 0;
				$code = 'name';
			} else {
                $this->clear_cache($this->cacheuri);
				$this->field_model->edit($_data, $data, $field->alter_sql($_data['fieldname'], $data['setting']['option']));
                $this->system_log('修改'.$this->name.'【'.$_data['fieldname'].'】'); // 记录日志
				$this->admin_msg(fc_lang('操作成功'), dr_url('field/index', array('rname' => $this->relatedname, 'rid' => $this->relatedid)), 1);
			}
			$data['fieldname'] = $_data['fieldname'];
			$data['fieldtype'] = $_data['fieldtype'];

		}

		$this->template->assign(array(
			'id' => $id,
			'page' => $page,
			'code' => $code,
			'data' => $data,
            'role' => $this->get_cache('role'),
			'ftype' => $ftype,
			'result' => $result,
			'relatedid' => $this->relatedid,
			'relatedname' => $this->relatedname,
		));
		$this->template->display('field_add.html');
	}
	
	/**
     * 通用操作
     */
    public function option() {

        $id = (int)$this->input->get('id');
        $data = $this->db->where('id', $id)->limit(1)->get('field')->row_array();
        switch ($this->input->get('op')) {
            case 'disabled':
                $value = $data['disabled'] == 1 ? 0 : 1;
                $this->db->where('id', $id)->update('field', array('disabled' => $value));
                $this->system_log(($value ? '禁用' : '启用').$this->name.'【'.$data['fieldname'].'】'); // 记录日志
                $this->clear_cache($this->cacheuri);
                exit(dr_json(1, fc_lang('操作成功')));
                break;
            case 'xss':
                $data['setting'] = dr_string2array($data['setting']);
                $data['setting']['validate']['xss'] = $value = $data['setting']['validate']['xss'] ? 0 : 1;
                $this->db->where('id', $id)->update('field', array(
                    'setting' => dr_array2string($data['setting'])
                ));
                $this->system_log($this->name.'【'.$data['fieldname'].'】'.($value ? '开启XSS' : '关闭XSS')); // 记录日志
                break;
            case 'member':
                $value = $data['ismember'] ? 0 : 1;
                $this->db->where('id', $id)->update('field', array(
                    'ismember' => $value
                ));
                $this->system_log($this->name.'【'.$data['fieldname'].'】'.($value ? '前端显示' : '前端隐藏')); // 记录日志
                break;
        }

        $this->clear_cache($this->cacheuri);
        $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('admin/field/index', array('rname'=>$this->relatedname, 'rid'=>$this->relatedid)), 1);
    }
	
	/**
     * 删除
     */
    public function del() {

        $id = (int)$this->input->get('id');
		$this->field_model->del(array($id));
        $this->system_log('删除'.$this->name.'【#'.$id.'】'); // 记录日志
        $this->clear_cache($this->cacheuri);

		exit(dr_json(1, fc_lang('操作成功')));
    }
}