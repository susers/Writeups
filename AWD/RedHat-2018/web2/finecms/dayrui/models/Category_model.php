<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Category_model extends M_Model {

    public $tablename;
    private	$categorys;

    public function __construct() {
        parent::__construct();
        $this->tablename = $this->db->dbprefix(SITE_ID.'_category');
    }

    public function get_permission($id) {
        $data = $this->db->where('id', $id)->select('permission')->get($this->tablename)->row_array();
        return dr_string2array($data['permission']);
    }

    public function get($id) {

        $data = $this->db->where('id', $id)->limit(1)->get($this->tablename)->row_array();
        if (isset($data['setting'])) {
            $data['setting'] = dr_string2array($data['setting']);
        }
        if (isset($data['permission'])) {
            $data['permission'] = dr_string2array($data['permission']);
        }

        return $data;
    }

    public function get_data() {

        $data = array();
        $_data = $this->db->order_by('displayorder ASC,id ASC')->get($this->tablename)->result_array();
        if (!$_data) {
            return $data;
        }

        foreach ($_data as $t) {
            $t['setting'] = dr_string2array($t['setting']);
            $t['permission'] = dr_string2array($t['permission']);
            $data[$t['id']]	= $t;
        }

        return $data;
    }

    public function add_all($names, $data, $field = array()) {

        if (!$names) {
            return 0;
        }

        $count = 0;
        $_data = explode(PHP_EOL, $names);

        foreach ($_data as $t) {

            list($name, $dir) = explode('|', $t);
            $data['name'] = trim($name);
            if (!$data['name']) {
                continue;
            } elseif ($data['tid'] == 1 && !$data['mid']) {
                continue;
            }

            !$dir && $dir = dr_word2pinyin($data['name']);
            $this->dirname_exitsts($dir) && $dir.= rand(0,99);

            $insert = array(
                'pid' => (int)$data['pid'],
                'pids' => '',
                'name' => $data['name'],
                'show' => $data['show'],
                'letter' => '',
                'setting' => dr_array2string($data['setting']),
                'dirname' => str_replace(array('/', '-', '_'), '', $dir),
                'pdirname' => '',
                'childids' => '',
                'displayorder' => 0
            );
            $field = $this->ci->get_table_field($this->tablename);
            foreach ($data as $i => $t) {
                isset($field[$i]) && !isset($insert[$i]) && $insert[$i] = $t;
            }
            $this->db->insert($this->tablename, $insert);

            $count ++;
        }

        $this->repair();

        return $count;
    }

    public function get_parent_mid($category, $id) {

        if (!isset($category[$id])) {
            return array();
        }

        $mid = '';
        $ids = @array_merge(explode(',',  $category[$id]['childids']), explode(',',  $category[$id]['pids']));
        foreach ($ids as $id) {
            if ($id && $category[$id] && $category[$id]['mid']) {
                $mid = $category[$id]['mid'];
                break;
            }
        }

        return array($mid, $ids);
    }


    public function add($data, $field = array()) {

        if (!$data || !$data['dirname']) {
            return fc_lang('目录不能为空');
        } elseif ($this->dirname_exitsts($data['dirname'])) {
            return fc_lang('目录已经存在了');
        } elseif ($data['tid'] == 1 && !$data['mid']) {
            return fc_lang('内容模型必须选择');
        }


        $mid = $data['tid'] == 1 ? $data['mid'] : '';
        $insert = array(
            'pid' => (int)$data['pid'],
            'tid' => (int)$data['tid'],
            'mid' => $mid,
            'pids' => '',
            'name' => trim($data['name']),
            'show' => $data['show'],
            'domain' => '',
            'letter' => '',
            'content' => $data['content'],
            'setting' => dr_array2string($data['setting']),
            'dirname' => str_replace(array('/', '-', '_'), '', $data['dirname']),
            'pdirname' => '',
            'childids' => '',
            'pcatpost' => (int)$data['pcatpost'],
            'displayorder' => 0
        );

        foreach ($data as $i => $t) {
            isset($field[$i]) && !isset($insert[$i]) && $insert[$i] = $t;
        }
        $this->db->insert($this->tablename, $insert);

        $id = $this->db->insert_id();
        $this->repair();


        return $id;
    }


    public function edit($id, $data, $_data, $field = array()) {

        if (!$data) {
            return fc_lang('栏目数据不存在');
        } elseif (!$data['dirname']) {
            return fc_lang('栏目目录不存在');
        } elseif ($this->dirname_exitsts($data['dirname'], $id)) {
            return fc_lang('目录已经存在了');
        }

        !isset($data['setting']['admin']) && $data['setting']['admin'] = array();
        !isset($data['setting']['member']) && $data['setting']['member'] = array();

        $permission = $data['rule'];
        if ($_data['permission']) {
            foreach ($_data['permission'] as $i => $t) {
                unset($t['show'], $t['forbidden'], $t['add'], $t['edit'], $t['del']);
                $permission[$i] = $permission[$i] ? $permission[$i] + $t : $t;
            }
        }
        $data['setting']['html'] = intval($data['setting']['html']);
        $data['setting']['getchild'] = intval($data['setting']['getchild']);
        $mid = $data['tid'] == 1 ? $data['mid'] : '';
        $update = array(
            'pid' => (int)$data['pid'],
            'tid' => (int)$data['tid'],
            'mid' => $mid,
            'name' => $data['name'],
            'show' => $data['show'],
            'domain' => $data['domain'] ? $data['domain'] : '',
            'letter' => $data['letter'] ? $data['letter'] : $data['dirname']{0},
            'content' => $data['content'],
            'dirname' => str_replace(array('/', '-', '_'), '', $data['dirname']),
            'setting' => dr_array2string(array_merge($_data['setting'], $data['setting'])),
            'pcatpost' => (int)$data['pcatpost'],
            'permission' => dr_array2string($permission)
        );

        foreach ($data as $i => $t) {
            isset($field[$i]) && !isset($update[$i]) && $update[$i] = $t;
        }

        $this->db->where('id', $id)->update($this->tablename, $update);
        $this->repair();


        return '';
    }

    public function syn($data, $_data) {


    }

    private function dirname_exitsts($dir, $id = 0) {
        return $dir ? $this->db->where('dirname', $dir)->where('id<>', $id)->count_all_results($this->tablename) : 1;
    }

    private function get_categorys($data = array()) {

        if (is_array($data) && !empty($data)) {
            foreach ($data as $catid => $c) {
                $this->categorys[$catid] = $c;
                $result = array();
                foreach ($this->categorys as $_k => $_v) {
                    $_v['pid'] && $result[] = $_v;
                }
            }
        }

        return true;
    }

    private function get_pids($catid, $pids = '', $n = 1) {

        if ($n > 5
            || !is_array($this->categorys)
            || !isset($this->categorys[$catid])) {
            return FALSE;
        }

        $pid = $this->categorys[$catid]['pid'];
        $pids = $pids ? $pid.','.$pids : $pid;

        if ($pid) {
            $pids = $this->get_pids($pid, $pids, ++$n);
        } else {
            $this->categorys[$catid]['pids'] = $pids;
        }

        return $pids;
    }

    private function get_childids($catid, $n = 1) {

        $childids = $catid;
        if ($n > 5
            || !is_array($this->categorys)
            || !isset($this->categorys[$catid])) {
            return $childids;
        }

        if (is_array($this->categorys)) {
            foreach ($this->categorys as $id => $cat) {
                if ($cat['pid']
                    && $id != $catid
                    && $cat['pid'] == $catid) {
                    $childids.= ','.$this->get_childids($id, ++$n);
                }
            }
        }

        return $childids;
    }

    public function get_pdirname($catid) {

        if ($this->categorys[$catid]['pid']==0) {
            return '';
        }

        $t = $this->categorys[$catid];
        $pids = $t['pids'];
        $pids = explode(',', $pids);
        $catdirs = array();
        krsort($pids);

        foreach ($pids as $id) {
            if ($id == 0) {
                continue;
            }
            $catdirs[] = $this->categorys[$id]['dirname'];
            if ($this->categorys[$id]['pdirname'] == '') {
                break;
            }
        }
        krsort($catdirs);

        return implode('/', $catdirs).'/';
    }

    /**
     * 格式化父级栏目模块mid
     */
    public function update_parent_mid($category, $catid) {

        if (!isset($category[$catid])) {
            return;
        }

        $ids = @explode(',',  $category[$catid]['childids']);
        if (!$ids) {
            return;
        }

        $mid = array();

        foreach ($ids as $id) {
            if ($id
                && $category[$id]
                && $category[$id]['tid'] == 1
                && $category[$id]['mid']) {
                $mid[] = $category[$id]['mid'];
            }
        }

        $mid && $mid = array_unique($mid);

        if (count($mid) > 1) {
            $this->db->where('id', (int)$catid)->update($this->tablename, array(
                'mid' => '',
                'tid' => 0
            ));
        }
    }

    public function repair() {

        $this->categorys = $categorys = array();
        $this->categorys = $categorys = $this->get_data();
        $this->get_categorys($categorys);

        if (is_array($this->categorys)) {

            foreach ($this->categorys as $catid => $cat) {
                $pids = $this->get_pids($catid);
                $childids = $this->get_childids($catid);
                $child = is_numeric($childids) ? 0 : 1;
                $pdirname = $this->get_pdirname($catid);

                $this->categorys[$catid]['pids'] = $pids;
                $this->categorys[$catid]['child'] = $child;
                $this->categorys[$catid]['childids'] = $childids;
                $this->categorys[$catid]['pdirname'] = $pdirname;

                if ($categorys[$catid]['pdirname'] != $pdirname
                    || $categorys[$catid]['pids'] != $pids
                    || $categorys[$catid]['childids'] != $childids
                    || $categorys[$catid]['child'] != $child) {
                    $this->db->where('id', $catid)->update($this->tablename, array(
                        'pids' => $pids,
                        'child' => $child,
                        'childids' => $childids,
                        'pdirname' => $pdirname
                    ));
                }

                if (defined('SYS_CAT_MODULE') && SYS_CAT_MODULE) {
                    // 栏目模型唯一开启后
                    if ($cat['mid'] && $cat['pid']) {
                        list($pmid, $ids) = $this->get_parent_mid($this->categorys, $cat['pid']);
                        $pmid && $this->db->where_in('id', $ids)->update($this->tablename, array('mid' => $pmid));
                    }
                } else {
                    $child && $this->update_parent_mid($this->categorys, $catid);
                }
            }
        }
    }

    public function cache() {

        foreach ($this->site_info as $siteid => $t) {
            $cache = array();
            $category = $this->db->order_by('displayorder ASC, id ASC')->get($siteid.'_category')->result_array();
            if ($category) {
                $CAT = $CAT_DIR = $level = array();
                foreach ($category as $c) {

                    $pid = explode(',', $c['pids']);
                    $level[] = substr_count($c['pids'], ',');
                    $c['mid'] = $c['tid'] == 1 ? $c['mid'] : '';
                    $c['topid'] = isset($pid[1]) ? $pid[1] : $c['id'];
                    $c['catids'] = explode(',', $c['childids']);
                    $c['domain'] = '';
                    $c['setting'] = dr_string2array($c['setting']);
                    $c['permission'] = '';
                    if ($c['tid'] != 2) {
                        $c['setting']['linkurl'] = "";
                    }
                    $c['url'] = isset($c['setting']['linkurl']) && $c['setting']['linkurl'] ? $c['setting']['linkurl'] : dr_category_url($c, $siteid);
                    $CAT[$c['id']] = $c;
                    $CAT_DIR[$c['dirname']] = $c['id'];
                }
                $cache['category'] = $CAT;
                $cache['category_dir'] = $CAT_DIR;
            } else {
                $cache['category'] = array();
                $cache['category_dir'] = array();
            }

            $field = $this->db
                ->where('disabled', 0)
                ->where('relatedid', $siteid)
                ->where('relatedname', 'category-share')
                ->order_by('displayorder ASC, id ASC')
                ->get('field')
                ->result_array();
            $cache['category_field'] = array();
            if ($field) {
                foreach ($field as $t) {
                    $t['setting'] = dr_string2array($t['setting']);
                    $cache['category_field'][$t['fieldname']] = $t;
                }
            }
            $this->dcache->set('category-field-'.$siteid, $cache['category_field']);

            $this->dcache->set('category-'.$siteid, $cache['category']);
            $this->dcache->set('category-dir-'.$siteid, $cache['category_dir']);
        }


    }
}