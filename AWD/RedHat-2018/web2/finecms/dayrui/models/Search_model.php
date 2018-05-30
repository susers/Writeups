<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Search_model extends M_Model {

    private $dir;

    /**
     * 搜索模型类
     */
    public function __construct() {
        parent::__construct();
        $this->sys_field = array(
            'id' => '',
            'uid' => '',
            'hits' => '',
            'catid' => '',
            'author' => '',
            'inputtime' => '',
            'updatetime' => '',
            'displayorder' => ''
        );
    }

    public function init($dir) {
        $this->dir = $dir;
    }

    /**
     * 搜索数据
     */
    public function get($get, $page = 1) {


        // 查询表名称
        $table = $this->db->dbprefix(SITE_ID.'_'.$this->dir);

        // 条件数组
        ksort($get);

        
        $order = $get['order'];
        $keyword = dr_strcut(dr_safe_replace($get['keyword']), 50);
        unset($get['order'], $get['keyword']);


        // 主表的字段
        $mod_field = $this->ci->module[$this->dir]['field'] ? $this->ci->module[$this->dir]['field'] + $this->sys_field : $this->sys_field;

        // 搜索关键字条件
        $where = array();
		$where[] = '`'.$table.'`.`status` = 9';
        if ($keyword != '') {
            $temp = array();
            $sfield = array('title', 'keywords');
            foreach ($sfield as $t) {
                $t && $temp[] = '`'.$table.'`.`'.$t.'` LIKE "%'.$this->db->escape_str($keyword).'%"';
            }
            $where[] = '('.implode(' OR ', $temp).')';
        }

        // 排序条件
        $_order = $order ? explode(',', $order) : array('displayorder', 'updatetime');
        $order_by = $_order_by = array();
        foreach ($_order as $i => $t) {
            $a = explode('_', $t);
            $b = end($a);
            if (in_array(strtolower($b), array('desc', 'asc'))) {
                $a = str_replace('_'.$b, '', $t);
            } else {
                $a = $t;
                $b = 'DESC';
            }
            isset($mod_field[$a]) && $_order_by[$a] = $b ? $b : "DESC";
        }
        !$_order_by && $_order_by['updatetime'] = 'DESC';
        unset($_order);

        // 字段过滤
        foreach ($mod_field as $name => $field) {
            if (isset($field['ismain']) && !$field['ismain']) {
                continue;
            }
            isset($get[$name]) && $get[$name] && $name != 'catid' && $where[] = $this->_where($table, $name, $get[$name], $field);
            // 地图坐标排序，这里不用它，默认id
            isset($_order_by[$name]) && $order_by[] = isset($field['fieldtype']) && $field['fieldtype'] == 'Baidumap' ? '`id` desc ' : '`'.$table.'`.`'.$name.'` '.$_order_by[$name];
        }

        $category = $this->ci->get_cache('category-'.SITE_ID);

        // 栏目的字段
        if ($get['catid']) {
            $where[0] = '`'.$table.'`.`catid`'.($category[$get['catid']]['child'] ? 'IN ('.$category[$get['catid']]['childids'].')' : '='.(int)$get['catid']);

        }

        // 筛选空值
        foreach ($where as $i => $t) {
            if (!$t) {
                unset($where[$i]);
            }
        }

        $where = $where ? 'WHERE '.implode(' AND ', $where) : '';

        // 组合sql查询结果
        $from = '`'.$table.'`';
        $sql = "SELECT * FROM {$from} {$where} ORDER BY ".implode(',', $order_by);
        $csql = "SELECT count(*) as c FROM {$from} {$where} ORDER BY NULL";

        // 统计
        $data = $this->db->query($csql)->row_array();
        if (!$data || !$data['c']) {
            return array(0, $sql);
        }

        return array($data['c'], $sql);
    }

    // 条件组合
    private function _where($table, $name, $value, $field) {
        if (strpos($value, '%') === 0
            && strrchr($value, '%') === '%') {
            // like 条件
            return '`'.$table.'`.`'.$name.'` LIKE "'.$this->db->escape_str($value).'"';
        } elseif (preg_match('/[0-9]+,[0-9]+/', $value)) {
            // BETWEEN 条件
            list($s, $e) = explode(',', $value);
            return '`'.$table.'`.`'.$name.'` BETWEEN '.(int)$s.' AND '.intval($e ? $e : SYS_TIME);
        } elseif (isset($field['fieldtype']) && $field['fieldtype'] == 'Linkage') {
            // 联动菜单
            $data = dr_linkage($field['setting']['option']['linkage'], $value);
            if ($data) {
                if ($data['child']) {
                    return '`'.$table.'`.`'.$name.'` IN ('.$data['childids'].')';
                } else {
                    return '`'.$table.'`.`'.$name.'`='.intval($data['ii']);
                }
            }
        } elseif (is_numeric($value)) {
            return '`'.$table.'`.`'.$name.'`='.$value;
        } else {
            return '`'.$table.'`.`'.$name.'`="'.$this->db->escape_str($value).'"';
        }
    }

}