<?php
/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Field_model extends M_Model {

    public $link;

    /**
     * 所有数据
     *
     * @return	void
     */
    public function get_data() {

        $data = $this->db->where('relatedid', $this->relatedid)->where('relatedname', $this->relatedname)->order_by('disabled ASC,displayorder ASC,id ASC')->get('field')->result_array();
        if (!$data) {
            return NULL;
        }

        foreach ($data as $i => $t) {
            $t['setting'] = dr_string2array($t['setting']);
            $data[$i] = $t;
        }

        return $data;
    }

    /**
     * 数据
     *
     * @param	int	$id
     * @return	array
     */
    public function get($id) {

        $data = $this->db->where('id', (int)$id)->where('relatedid', $this->relatedid)->where('relatedname', $this->relatedname)->get('field')->row_array();
        if (!$data) {
            return NULL;
        }

        $data['setting'] = dr_string2array($data['setting']);

        return $data;
    }

    /**
     * 添加字段
     *
     * @param	array	$data
     * @param	string	$sql
     * @return	void
     */
    public function add($data, $sql) {

        // 当为编辑器类型时，关闭xss过滤
        $data['fieldtype'] == 'Ueditor' && $data['setting']['validate']['xss'] = 1;

        $data['ismain'] = (int)$data['ismain'];
        $data['setting'] = dr_array2string($data['setting']);
        $data['issystem'] = 0;
        $data['issearch'] = (int)$data['issearch'];
        $data['ismember'] = (int)$data['ismember'];
        $data['disabled'] = (int)$data['disabled'];
        $data['relatedid'] = $this->relatedid;
        $data['relatedname'] = $this->relatedname;
        $data['displayorder'] = (int)$data['displayorder'];

        // 入库字段表
        $this->db->insert('field', $data);

        // 执行数据库语句
        $sql && $this->update_table($sql, $data['ismain']);
    }

    /**
     * 修改字段
     *
     * @param	array	$_data	旧数据
     * @param	array	$data	新数据
     * @param	string	$sql	执行该操作的sql语句
     * @return	string
     */
    public function edit($_data, $data, $sql) {

        if (!$_data || !$data) {
            return NULL;
        }

        // 如果字段类型、长度变化时，分别更新各站点
        ($data['setting']['option']['fieldtype'] != $_data['setting']['option']['fieldtype']
            || $data['setting']['option']['fieldlength'] != $_data['setting']['option']['fieldlength']) 
            && $this->update_table($sql, $_data['ismain']);

        $data['setting'] = dr_array2string($data['setting']);
        $data['issearch'] = (int)$data['issearch'];
        $data['ismember'] = (int)$data['ismember'];
        $data['disabled'] = (int)$data['disabled'];

        // 更新字段表
        $this->db->where('id', $_data['id'])->update('field', $data);

    }

    /**
     * 分别更新各站点的表结构
     *
     * @param	string	$sql		执行该操作的sql语句
     * @param	intval	$ismain		是否主表
     * @return	void
     */
    public function update_table($sql, $ismain) {

        if (!$sql) {
            return NULL;
        }

        call_user_func_array(array($this, '_sql_'.$this->func), array($sql, $ismain));
        return;
    }

    /*
     * 判断同表字段否存在
     *
     * @param	string	$name	字段名称
     * @param	intval	$int	字段id
     * @return	int
     */
    public function exitsts($name) {

        if (!$name)	{
            return 1;
        }

        $tableinfo = $this->ci->get_cache('table');
        if (!$tableinfo) {
            $this->load->model('system_model');
            $tableinfo = $this->system_model->cache(); // 表结构缓存
        }

        return call_user_func_array(array($this, '_field_'.$this->func), array($tableinfo, $name));
    }

    /**
     * 删除字段
     *
     * @param	array	$ids
     * @return	bool
     */
    public function del($ids) {

        if (!$ids) {
            return FALSE;
        }

        !is_array($ids) && $ids = array($ids);

        foreach ($ids as $id) {
            $data = $this->get($id);
            $field = $this->dfield->get($data['fieldtype']);
            if ($field) {
                // 非系统字段才支持删除
                if ($data['issystem'] == 1 && in_array($data['fieldname'], array(
                    'title', 'keywords', 'description', 'subject'
                    ))) {
                    continue;
                }
                $this->db->where('id', $id)->delete('field');
                $sql = $field->drop_sql($data['fieldname']);
                // 需要分别更新各站点
                $sql && $this->update_table($sql, $data['ismain']);
            }
        }

        return TRUE;
    }

    ////////////////////////////////////////////////////



    // 模型字段
    private function _sql_module($sql, $ismain) {
        // 更新站点模型
        foreach ($this->ci->site_info as $sid => $t) {
            if (!$this->db->where('dirname', $this->data['dirname'])->count_all_results('module')) {
                continue;
            }
            $table = $this->db->dbprefix($sid.'_'.$this->data['dirname']); // 主表名称
            if ($ismain) {
                $this->db->query(str_replace('{tablename}', $table, $sql));
            } else {
                $this->db->query(str_replace('{tablename}', $table.'_data_0', $sql)); //执行更新语句
            }
        }
    }
    private function _field_module($tableinfo, $name) {
        $_field	= $this->db->where('fieldname', $name)->where('relatedid', $this->relatedid)->where('relatedname', $this->relatedname)->count_all_results('field');
        if (!$_field) {
            $module = get_module($this->data['dirname'], SITE_ID);
            $_field = isset($module['field'][$name]) ? 1 : 0;
        }
        $_system = $tableinfo[$this->db->dbprefix(SITE_ID.'_'.$this->data['dirname'])]['field'];
        return $_field ? 1 : (isset($_system[$name]) ? 1 : 0);
    }

    // 网站表单字段
    private function _sql_form($sql, $ismain) {
        $table = $this->db->dbprefix(SITE_ID.'_form_'.$this->data['table']); // 主表名称
        if ($ismain) {
            // 更新主表 格式: 站点id_名称
            $this->db->query(str_replace('{tablename}', $table, $sql));
        } else {
            for ($i = 0; $i < 100; $i ++) {
                if (!$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$i."'")->row_array()) {
                    break;
                }
                $this->db->query(str_replace('{tablename}', $table.'_data_'.$i, $sql)); //执行更新语句
            }
        }
    }
    private function _field_form($tableinfo, $name) {
        $_field	= $this->db->where('fieldname', $name)->where('relatedid', $this->relatedid)->where('relatedname', 'form')->count_all_results('field');
        $_system = $tableinfo[$this->db->dbprefix(SITE_ID.'_form_'.$this->data['table'])]['field'];
        return $_field ? 1 : (isset($_system[$name]) ? 1 : 0);
    }





    // 模型栏目表字段
    private function _sql_category_info($sql, $ismain) {
        $table = $this->db->dbprefix(SITE_ID.'_category');
        $this->db->query(str_replace('{tablename}', $table, $sql));
    }
    private function _field_category_info($tableinfo, $name) {
        $_field	= $this->db->where('fieldname', $name)->where('relatedid', $this->relatedid)->where('relatedname', $this->relatedname)->count_all_results('field');
        $_system = $tableinfo[$this->db->dbprefix(SITE_ID.'_'.$this->data['dirname'].'_category')]['field'];
        return $_field ? 1 : (isset($_system[$name]) ? 1 : 0);
    }

    // 会员字段
    private function _sql_member($sql, $ismain) {
        $table = $this->db->dbprefix('member_data'); // 会员表名称
        $this->db->query(str_replace('{tablename}', $table, $sql));
    }
    private function _field_member($tableinfo, $name) {
        $_field	= $this->db->where('fieldname', $name)->where('relatedname', 'member')->count_all_results('field');
        $_system = $tableinfo[$this->db->dbprefix('member')]['field'];
        return $_field ? 1 : (isset($_system[$name]) ? 1 : 0);
    }


    // 任意表字段
    private function _sql_table($sql, $ismain) {
        $table = $this->db->dbprefix($this->data['table']); // 主表名称
        $this->db->query(str_replace('{tablename}', $table, $sql)); //执行更新语句
    }
    private function _field_table($tableinfo, $name) {
        $_field	= $this->db->where('fieldname', $name)->where('relatedid', $this->relatedid)->where('relatedname', $this->relatedname)->count_all_results('field');
        $_system = $tableinfo[$this->db->dbprefix($this->data['table'])]['field'];
        return $_field ? 1 : (isset($_system[$name]) ? 1 : 0);
    }

}