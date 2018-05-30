<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Content_model extends M_Model {

    public $mdir;
    public $prefix;
    public $module;
    public $cache_file;


    public function init($module) {
        $this->mdir = $module['dirname'];
        $this->module = $module;
        $this->prefix = $this->db->dbprefix(SITE_ID.'_'.$this->mdir);

    }

    private function _where(&$select, $data) {

        // 存在POST提交时，重新生成缓存文件
        if (IS_POST) {
            $data = $this->input->post('data');
            foreach ($data as $i => $t) {
                if ($t == '') {
                    unset($data[$i]);
                }
            }
            unset($_GET['page']);
        }

        // 存在search参数时，读取缓存文件
        if ($data) {
            if (isset($data['keyword']) && $data['keyword'] != '' && $data['field']) {
                $field = $this->field ? $this->field : $this->ci->module[$this->mdir]['field'];
                if ($data['field'] == 'id') {
                    // 按id查询
                    $id = array();
                    $ids = explode(',', $data['keyword']);
                    foreach ($ids as $i) {
                        $id[] = (int) $i;
                    }
                    $select->where_in('id', $id);
                } elseif ($field[$data['field']]['fieldtype'] == 'Linkage'
                    && $field[$data['field']]['setting']['option']['linkage']) {
                    // 联动菜单搜索
                    if (is_numeric($data['keyword'])) {
                        // 联动菜单id查询
                        $link = dr_linkage($field[$data['field']]['setting']['option']['linkage'], (int)$data['keyword'], 0, 'childids');
                        $link && $select->where($data['field'].' IN ('.$link.')');
                    } else {
                        // 联动菜单名称查询
                        $id = (int)$this->ci->get_cache('linkid-'.SITE_ID, $field[$data['field']]['setting']['option']['linkage']);
                        $id && $select->where($data['field'].' IN (select id from `'.$select->dbprefix('linkage_data_'.$id).'` where `name` like "%'.$data['keyword'].'%")');
                    }
                } else {
                    $select->like($data['field'], urldecode($data['keyword']));
                }
            }
            // 时间搜索
            if (isset($data['start']) && $data['start']) {
                $data['end'] = strtotime(date('Y-m-d 23:59:59', $data['end'] ? $data['end'] : SYS_TIME));
                $data['start'] = strtotime(date('Y-m-d 00:00:00', $data['start']));
                $select->where('updatetime BETWEEN '.$data['start'].' AND '.$data['end']);
            } elseif (isset($data['end']) && $data['end']) {
                $data['end'] = strtotime(date('Y-m-d 23:59:59', $data['end']));
                $data['start'] = 1;
                $select->where('updatetime BETWEEN '.$data['start'].' AND '.$data['end']);
            }
        }

        isset($data['flag']) && $select->where('flag', $data['flag']);

        if (isset($data['catid']) && $data['catid']) {
            $cat = $this->ci->get_cache('category-'.SITE_ID, $data['catid']);
            $cat['child'] ? $select->where_in('catid', explode(',', $cat['childids'])) : $select->where('catid', $data['catid']);
        }

        $this->where && $select->where($this->where);

        return $data;
    }

    public function limit_page($param, $page, $total) {

        if (!$total || IS_POST) {
            $select = $this->db->select('count(*) as total');
            $_param = $this->_where($select, $param);
            $_param && $select->order_by('id');
            $odb = $select->get($this->prefix);
            if (!$odb) {
                $_param['total'] = 0;
                return array(array(), $_param);
            }
            $data = $odb->row_array();
            unset($select);
            $total = (int)$data['total'];
            if (!$total) {
                $_param['total'] = 0;
                return array(array(), $_param);
            }
            $page = 1;
        }

        $select = $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $_param = $this->_where($select, $param);
        $_order = dr_get_order_string($this->input->get('order'), 'updatetime desc');
        $data = $select->order_by($_order)->get($this->prefix)->result_array();
        $_param['total'] = $total;
        $_param['order'] = $_order;

        return array($data, $_param);
    }

    public function index($data) {
        $this->db->insert(SITE_ID.'_index', array(
            'uid' => $data[1]['uid'],
            'mid' => $this->mdir,
            'catid' => $data[1]['catid'],
            'status' => $data[1]['status'],
            'inputtime' => $data[1]['inputtime'],
        ));
        return $this->db->insert_id();
    }

    public function add($data) {

        // 生成索引id
        $data[0]['id'] = $data[1]['id'] = $id = $this->index($data);
        $data[0]['uid'] = (int)$data[1]['uid'];
        $data[1]['hits'] = (int)$data[1]['hits'];
        $data[0]['catid'] = (int)$data[1]['catid'];
        $data[1]['comments'] = 0;
        $data[1]['favorites'] = 0;

        if (!$id) {
            return FALSE;
        }

        $field = $this->ci->get_table_field($this->prefix);

        $data[1]['tableid'] = 0;

        // 格式化字段值
        $data = $this->get_content_data($data);
        $data[1]['keywords'] = str_replace(array('，', '、', '；', ';'), ',', $data[1]['keywords']);

        if ($data[1]['status'] >= 9) {
            // 审核通过

            // 判断描述字段
            if (!isset($field['description'])) {
                $data[0]['description'] = $data[1]['description'];
                unset($data[1]['description']);
            }

            $data[1]['url'] = dr_show_url($data[1]);

            $this->db->replace($this->prefix, $data[1]); // 主表

            $this->db->replace($this->prefix.'_data_0', $data[0]); // 副表


        } else {

        }



        return $id;
    }

    // 修改
    public function edit($_data, $data, $oid = 0) {

        // 参数判断
        if (!$data || !$_data) {
            return FALSE;
        }



        // 修改之前挂钩点
        $data['edit'] = $_data;
        $data[1]['hits'] = (int)$_data['hits'];
        $data[1]['comments'] = (int)$_data['comments'];
        $data[1]['favorites'] = (int)$_data['favorites'];
        unset($data['edit']);

        // 格式化字段值
        $data = $this->get_content_data($data, $_data);
        $data[1]['keywords'] = str_replace(array('，', '、', '；', ';'), ',', $data[1]['keywords']);
        if ($data[1]['status'] >= 9) {
            $field = $this->ci->get_table_field($this->prefix);
            // 判断描述字段的归属
            if (!isset($field['description'])) {
                $data[0]['description'] = $data[1]['description'];
                unset($data[1]['description']);
            }
            // 会员不等时表示在修改会员
            $_uid = intval($_data['uid']);
            $_uid != $data[1]['uid'] && $this->db->where('id', intval($_data['id']))->update(SITE_ID.'_index', array(
                'uid' => $_uid,
            ));
            // 生成url地址
            $data[1]['url'] = dr_show_url(array_merge($_data, $data[1]));
            // 更新索引表
            $data[1]['status'] = intval($data[1]['status']);
            $this->db->where('id', $_data['id'])->update(SITE_ID.'_index', array(
                'uid' => $data[1]['uid'],
                'catid' => $data[1]['catid'],
                'status' => $data[1]['status']
            ));
            // 提交为审核通过状态
            $data[1]['id'] = $data[0]['id'] = $_data['id'];
            $data[0]['uid'] = $data[1]['uid'];
            $data[0]['catid'] = $data[1]['catid'];
            // 副表以5w左右数据量无限分表
            $data[1]['tableid'] = 0;

            $this->db->where('id', $_data['id'])->count_all_results($this->prefix) ? $this->db->where('id', $_data['id'])->update($this->prefix, $data[1]) : $this->db->replace($this->prefix, $data[1]);
            // 副表
            $this->db->replace($this->prefix.'_data_0', $data[0]);


        } else {

        }

        $this->ci->clear_cache('hits'. $this->mdir.SITE_ID.$_data['id']);
        $this->ci->clear_cache('show'.$this->mdir.SITE_ID.$_data['id']);
        $this->ci->clear_cache('mshow'.$this->mdir.SITE_ID.$_data['id']);

        // 修改之后挂钩点
        $data['edit'] = $_data;

        // 修改之后执行的方法
        $this->_edit_content($data);

        return $_data['id'];
    }




    // 获取内容
    public function get($id) {

        if (!$id) {
            return NULL;
        }

        // 主表
        $data1 = $this->db->where('id', $id)->limit(1)->get($this->prefix)->row_array();
        if (!$data1) {
            return NULL;
        }

        // 副表
        $data2 = $this->db->where('id', $id)->limit(1)->get($this->prefix.'_data_0')->row_array();

        // 数据组合
        $data = $data2 ? $data1 + $data2 : $data1;

        return $data;
    }




    /**
     * 删除内容
     *
     * @param	intval	$id			模型内容的id
     * @param	intval	$tableid	模型内容附表id
     * @return  NULL
     */
    public function delete_for_id($id, $tableid = 0) {

        if (!$id) {
            return NULL;
        }


        // 删除缓存
        $this->ci->clear_cache('hits'. $this->mdir.SITE_ID.$id);
        $this->ci->clear_cache((SITE_MOBILE === TRUE ? 'm' : '').'show'.$this->mdir.SITE_ID.$id);



        // 删除附表表
        $this->db->where('id', $id)->delete($this->prefix.'_data_0');
        // 删除主表
        $this->db->where('id', $id)->delete($this->prefix);


    }



    // 更新文档时间
    public function updatetime($id) {
        $this->db->where('uid', $this->uid)->where_in('id', $id)->update($this->prefix, array('updatetime' => SYS_TIME));
    }

    // 移动栏目
    public function move($id, $catid) {

        if (!$id || !$catid) {
            return FALSE;
        }

        $this->db->where_in('id', $id)->update($this->prefix, array('catid' => $catid));
        $this->db->where_in('id', $id)->update(SITE_ID.'_index', array('catid' => $catid));


        return TRUE;
    }



    // 获取内容（用于商品订单），模型可重写
    public function get_item_data($id) {
        return NULL;
    }

    // 格式化字段值，模型可重写
    protected function get_content_data($data, $_data = NULL) {
        
        !$data[1]['description'] && $data[1]['description'] = trim(dr_strcut(dr_clearhtml($data[0]['content']), 200));
        
        return $data;
    }

    // 格式化字段值，模型可重写
    protected function get_content_extend_data($data, $_data = NULL) {
        return $data;
    }



    // 以下方法用于二次开发或扩展
    public function _add_content($data) { }
    public function _edit_content($data) { }
    public function _del_content($data) { }
    public function _update_status($data) { }
    public function _update_status_extend($data) { }

}
