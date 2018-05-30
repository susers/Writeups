<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */
	
class Module_model extends M_Model {
	
	public $system_table; // 系统默认表
    public $system_field;
	
	/*
	 * 模型模型类
	 */
    public function __construct() {
        parent::__construct();
        $this->system_field =
            array (
                0 =>
                    array (
                        'fieldname' => 'title',
                        'fieldtype' => 'Text',
                        'relatedname' => 'module',
                        'isedit' => '1',
                        'ismain' => '1',
                        'issystem' => 1,
                        'ismember' => '1',
                        'issearch' => '1',
                        'disabled' => '0',
                        'setting' =>
                            array (
                                'option' =>
                                    array (
                                        'width' => 400,
                                        'fieldtype' => 'VARCHAR',
                                        'fieldlength' => '255',
                                    ),
                                'validate' =>
                                    array (
                                        'xss' => 1,
                                        'required' => 1,
                                        'formattr' => 'onblur="check_title();get_keywords(\'keywords\');"',
                                    ),
                            ),
                        'displayorder' => '0',
                        'textname' => '主题',
                    ),
                1 =>
                    array (
                        'fieldname' => 'thumb',
                        'fieldtype' => 'File',
                        'relatedname' => 'module',
                        'isedit' => '1',
                        'ismain' => '1',
                        'issystem' => 1,
                        'ismember' => '1',
                        'issearch' => '1',
                        'disabled' => '0',
                        'setting' =>
                            array (
                                'option' =>
                                    array (
                                        'ext' => 'jpg,gif,png',
                                        'size' => 10,
                                        'width' => 400,
                                        'fieldtype' => 'VARCHAR',
                                        'fieldlength' => '255',
                                    ),
                            ),
                        'displayorder' => '0',
                        'textname' => '缩略图',
                    ),
                2 =>
                    array (
                        'fieldname' => 'keywords',
                        'fieldtype' => 'Text',
                        'relatedname' => 'module',
                        'isedit' => '1',
                        'ismain' => '1',
                        'issystem' => 1,
                        'ismember' => '1',
                        'issearch' => '1',
                        'disabled' => '0',
                        'setting' =>
                            array (
                                'option' =>
                                    array (
                                        'width' => 400,
                                        'fieldtype' => 'VARCHAR',
                                        'fieldlength' => '255',
                                    ),
                                'validate' =>
                                    array (
                                        'xss' => 1,
                                        'formattr' => ' data-role="tagsinput"', // tag属性
                                    ),
                            ),
                        'displayorder' => '0',
                        'textname' => '关键字',
                    ),
                3 =>
                    array (
                        'fieldname' => 'description',
                        'fieldtype' => 'Textarea',
                        'relatedname' => 'module',
                        'isedit' => '1',
                        'ismain' => '1',
                        'issystem' => 1,
                        'ismember' => '1',
                        'issearch' => '1',
                        'disabled' => '0',
                        'setting' =>
                            array (
                                'option' =>
                                    array (
                                        'width' => 500,
                                        'height' => 60,
                                        'fieldtype' => 'VARCHAR',
                                        'fieldlength' => '255',
                                    ),
                                'validate' =>
                                    array (
                                        'xss' => 1,
                                        'filter' => 'dr_clearhtml',
                                    ),
                            ),
                        'displayorder' => '0',
                        'textname' => '描述',
                    ),
                4 =>
                    array (
                        'fieldname' => 'content',
                        'fieldtype' => 'Ueditor',
                        'relatedname' => 'module',
                        'isedit' => '1',
                        'ismain' => '0',
                        'issystem' => 1,
                        'ismember' => '1',
                        'issearch' => '1',
                        'disabled' => '0',
                        'setting' =>
                            array (
                                'option' =>
                                    array (
                                        'mode' => 1,
                                        'width' => '90%',
                                        'height' => 400,
                                    ),
                                'validate' =>
                                    array (
                                        'xss' => 1,
                                        'required' => 1,
                                    ),
                            ),
                        'displayorder' => '0',
                        'textname' => '内容',
                    ),

            );
		$this->system_table = array(
			'' =>  "CREATE TABLE IF NOT EXISTS `{tablename}` (
  `id` int(10) unsigned NOT NULL,
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `title` varchar(255) DEFAULT NULL COMMENT '主题',
  `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键字',
  `description` text COMMENT '描述',
  `hits` mediumint(8) unsigned DEFAULT NULL COMMENT '浏览数',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者id',
  `author` varchar(50) NOT NULL COMMENT '作者名称',
  `status` tinyint(2) NOT NULL COMMENT '状态',
  `url` varchar(255) DEFAULT NULL COMMENT '地址',
  `tableid` smallint(5) unsigned NOT NULL COMMENT '附表id',
  `inputip` varchar(15) DEFAULT NULL COMMENT '录入者ip',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  `comments` int(10) unsigned NOT NULL COMMENT '评论数量',
  `favorites` int(10) unsigned NOT NULL COMMENT '收藏数量',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`),
  KEY `updatetime` (`updatetime`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模型主表';",

			'_data_0' => "
CREATE TABLE IF NOT EXISTS `{tablename}` (
  `id` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `content` mediumtext COMMENT '内容',
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模型附表';",





		);	
	}
	
	/**
	 * 所有模型
	 *
	 * @return	array
	 */
	public function get_data() {

		$_data = $this->db->order_by('displayorder ASC,id ASC')->get('module')->result_array();
		if (!$_data) {
            return NULL;
        }

		$data = array();
		foreach ($_data as $t) {
			$t['site'] = dr_string2array($t['site']);
			$t['setting'] = dr_string2array($t['setting']);
			$data[$t['dirname']] = $t;
		}

		return $data;
	}
	
	/**
	 * 模型数据
	 *
	 * @param	int		$id
	 * @return	array
	 */
	public function get($id) {

		if (is_numeric($id)) {
			$this->db->where('id', (int)$id);
		} else {
			$this->db->where('dirname', (string)$id);
		}
		$data = $this->db->limit(1)->get('module')->row_array();
		if (!$data) {
            return NULL;
        }

		$data['site'] = dr_string2array($data['site']);
		$data['setting'] = dr_string2array($data['setting']);


		return $data;
	}
	
	/**
	 * 模型入库
	 */
	public function add($name, $dir) {

		if (!$dir) {
            return '表名不存在';
        } elseif ($this->db->where('dirname', $dir)->count_all_results('module')) {
			// 判断重复安装
            return '表名已经存在';
        }

		$m = array(
			'site' => dr_array2string(array(
			    'name' => $name,
            )),
			'share' => 0,
			'extend' => 0,
			'dirname' => $dir,
			'setting' => '',
			'sitemap' => 0,
			'disabled' => 0,
			'displayorder' => 0,
		);
		$this->db->replace('module', $m);
		$id = $this->db->insert_id();

		if (!$id) {
            return '安装失败';
        }


        $this->install($id, $dir, SITE_ID);

		return '';
	}
	

	
	/**
	 * 字段入库
	 * @return	bool
	 */
	private function add_field($id, $field) {

		$rname = 'module';
		if ($this->db->where('fieldname', $field['fieldname'])->where('relatedid', $id)->where('relatedname', $rname)->count_all_results('field')) {
			return;
		}

		$this->db->insert('field', array(
			'name' => $field['textname'],
			'ismain' => isset($field['ismain']) ? (int)$field['ismain'] : 1,
			'setting' => dr_array2string($field['setting']),
			'issystem' => isset($field['issystem']) ? (int)$field['issystem'] : 1,
			'ismember' => isset($field['ismember']) ? (int)$field['ismember'] : 1,
			'disabled' => isset($field['disabled']) ? (int)$field['disabled'] : 0,
			'fieldname' => $field['fieldname'],
			'fieldtype' => $field['fieldtype'],
			'relatedid' => $id,
			'relatedname' => $rname,
			'displayorder' => (int)$field['displayorder'],
		));
	}
	
	/**
	 * 安装到站点
	 */
	public function install($id, $dir, $siteid = SITE_ID) {

        if (!$dir || !$siteid || !isset($this->site[$siteid])) {
            return NULL;
        }

		// 表前缀部分：站点id_模型目录[_表名称]
		$prefix = $this->db->dbprefix($siteid.'_'.$dir);

        // 系统默认表
        foreach ($this->system_table as $table => $sql) {
            $this->db->query('DROP TABLE IF EXISTS `'.$prefix.$table.'`');
            $this->db->query(trim(str_replace('{tablename}', $prefix.$table, $sql)));
        }

        foreach ($this->system_field as $field) {
            $this->add_field($id, $field);
        }

	}
	
	/**
	 * 从站点中卸载
	 */
	public function uninstall($dir, $siteid = SITE_ID) {
	
		if (!$dir || !$siteid) {
            return NULL;
        }

        $prefix = $this->db->dbprefix($siteid.'_'.$dir);

        // 系统默认表
        foreach ($this->system_table as $table => $sql) {
            $this->db->query('DROP TABLE IF EXISTS `'.$prefix.$table.'`');
        }

        return '';

	}

	
	/**
	 * 修改
	 *
	 * @param	array	$_data	老数据
	 * @param	array	$data	新数据
	 * @return	void
	 */
	public function edit($id, $data) {
		$this->db->where('id', $id)->update('module', array(
            'sitemap' => (int)$data['sitemap'],
            'setting' => dr_array2string($data['setting'])
         ));
	}
	
	/**
	 * 删除
	 *
	 * @param	intval	$id
	 * @return	void
	 */
	public function del($id) {
		// 模型信息
		$data = $this->get($id);
		if (!$data) {
            return NULL;
        }
		// 删除模型数据和卸载全部站点
		foreach ($this->site_info as $siteid => $t) {
			$this->uninstall($data['dirname'], $siteid);
		}
        $this->db->where('id', $id)->delete('module');
		// 删除模型字段
		$this->db->where('relatedname', 'module')->where('relatedid', $id)->delete('field');
	}
	
	/**
	 * 格式化字段数据
	 *
	 * @param	array	$data	新数据
	 * @return	array
	 */
	private function get_field_value($data) {
		if (!$data) {
            return NULL;
        }
		$data['setting'] = dr_string2array($data['setting']);
		return $data;
	}

	public function install_table($dirname, $siteid) {

        // 判断模块表是否存在
        if (!$this->db->table_exists($siteid.'_'.$dirname)) {
            $mark2 = $mark1 = 0;
            foreach ($this->site_info as $sid => $c) {
                if ($this->db->table_exists($sid.'_'.$dirname)) {
                    $mark1 = 1;
                    $sql = $this->db->query("SHOW CREATE TABLE `".$this->db->dbprefix($sid.'_'.$dirname)."`")->row_array();
                    $this->db->query(str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array($this->db->dbprefix($siteid.'_'.$dirname), 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    ));
                }
                if ($this->db->table_exists($sid.'_'.$dirname.'_data_0')) {
                    $mark2 = 1;
                    $sql = $this->db->query("SHOW CREATE TABLE `".$this->db->dbprefix($sid.'_'.$dirname)."_data_0`")->row_array();
                    $this->db->query(str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array($this->db->dbprefix($siteid.'_'.$dirname).'_data_0', 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    ));
                }
            }
            if (!$mark1 || !$mark2) {
                // 系统默认表
                foreach ($this->system_table as $table => $sql) {
                    $this->db->query(trim(str_replace('{tablename}', $this->db->dbprefix($siteid.'_'.$dirname).$table, $sql)));
                }
            }
        }

    }
	
	/**
	 * 模型缓存
	 */
	public function cache() {

        $cache = array();
	    $module = $this->get_data();
	    foreach ($module as $dirname => $data) {
            // 模型的自定义字段
            $field = $this->db
                ->where('disabled', 0)
                ->where('relatedid', $data['id'])
                ->where('relatedname', 'module')
                ->order_by('displayorder ASC, id ASC')
                ->get('field')->result_array();
            if ($field) {
                foreach ($field as $f) {
                    $data['field'][$f['fieldname']] = $this->get_field_value($f);
                }
            } else {
                $data['field'] = array();
            }

            $this->install_table($dirname, SITE_ID);

            $data['name'] = $data['site']['name'] ? $data['site']['name'] : '未命名';
            $cache[$dirname] = $data;
	    }


        $this->dcache->set('module', $cache);


	    return $cache;
	}
}