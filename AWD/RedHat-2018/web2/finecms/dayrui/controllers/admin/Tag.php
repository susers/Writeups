<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

require FCPATH.'dayrui/core/M_Table.php';

class Tag extends M_Table {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('tag_model');
        $this->pid = intval($_GET['pid']);
        $this->mydb = $this->db; // 数据库
        $this->tfield = '`displayorder`'; // 时间字段用于搜索和排序
        $this->mytable = SITE_ID.'_tag';
        $this->mywhere = 'pid='.$this->pid;
        $this->post_param = array('pid' => $this->pid);
        $this->myfield = array(
            'name' => array(
                'ismain' => 1,
                'name' => fc_lang('名称'),
                'fieldname' => 'name',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                    ),
                    'is_right' => 2,
                )
            ),
            'displayorder' => array(
                'name' => fc_lang('权重值'),
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'displayorder',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                        'value' => 0
                    ),
                    'validate' => array(
                        'tips' => fc_lang('权重值越高排列越靠前'),
                    )
                )
            ),
            'content' => array(
                'ismain' => 1,
                'name' => fc_lang('描述'),
                'fieldname' => 'content',
                'fieldtype' => 'Ueditor',
                'setting' => array(
                    'option' => array(
                        'mode' => 1,
                        'height' => 300,
                        'width' => '100%'
                    ),
                )
            ),
        );
        $field['name'] = $this->myfield['name'];
        $field['content'] = $this->myfield['content'];
        //$this->myfield = dr_array22array($this->myfield, $this->get_cache('tag-'.SITE_ID.'-field'));
        $this->template->assign(array(
            'pid' => $this->pid,
            'field' => $field,
            'menu' => $this->get_menu_v3(array(
                fc_lang('关键词库') => array('admin/tag/index', 'tag'),
                fc_lang('添加') => array('admin/tag/add', 'plus'),
                fc_lang('批量添加') => array('admin/tag/all_add', 'plus-square-o'),
                fc_lang('更新缓存') => array(APP_DIR.'/admin/tag/cache', 'refresh'),
                //'自定义字段' => array('admin/field/index/rname/tag/rid/'.SITE_ID, 'plus-square'),
            )),
        ));
    }

    /**
     * 管理
     */
    public function index() {

        if (IS_POST && $this->input->post('action') == 'del') {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            $this->db->where_in('id', $ids)->delete($this->mytable);
            $this->db->where_in('pid', $ids)->delete($this->mytable);
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $this->_index();
        $this->template->display('tag_list.html');
    }


    public function add() {

        $this->_add();
        $this->template->display('tag_post.html');
    }

    public function edit() {


        $this->_edit();
        $this->template->assign(array(
            'pid' => $this->pid,
        ));
        $this->template->display('tag_post.html');
    }

    // 后台批量添加内容
    public function all_add() {

        if (IS_POST) {
            $rt = $this->tag_model->save_all_data($this->pid, $_POST['all']);
            $this->admin_msg($rt, dr_url($this->router->class.'/index', $this->post_param), 1);
        }

        $this->template->display('tag_all.html');
    }

    // 重写:获取数据
    public function _get_data($id) {
        $data = $this->mydb->where($this->myid, intval($id))->get($this->mytable)->row_array();
        $this->pid = $data['pid'];
        $this->post_param = array('pid' => $data['pid']);
        return $data;
    }

    // 插入数据
    public function _insert_data($data) {


        $data['pid'] = (int)$this->pid;
        $data['name'] = dr_safe_replace($_POST['data']['name']);
        $data['code'] = dr_safe_replace($_POST['data']['code']);
        $data['hits'] = intval($_POST['data']['hits']);

        if ($this->tag_model->check_code(0, $data['code'])) {
            $this->admin_msg(fc_lang('别名已经存在'));
        } elseif ($this->tag_model->check_name(0, $data['name'])) {
            $this->admin_msg(fc_lang('关键词名称已经存在'));
        }
        
        $data['childids'] = '';
        $data['pcode'] = '';
        $data['url'] = '';
        $data['total'] = 0;

        $this->mydb->insert($this->mytable, $data);

        return $this->mydb->insert_id();
    }

    // 重写:更新数据
    public function _update_data($id, $data, $_data) {

        $data['hits'] = intval($_POST['data']['hits']);
        $data['name'] = dr_safe_replace($_POST['data']['name']);
        $data['code'] = dr_safe_replace($_POST['data']['code']);

        if ($this->tag_model->check_code($id, $data['code'])) {
            $this->admin_msg(fc_lang('别名已经存在'));
        } elseif ($this->tag_model->check_name($id, $data['name'])) {
            $this->admin_msg(fc_lang('关键词名称已经存在'));
        }

        $this->mydb->where($this->myid, $id)->update($this->mytable, $data);
    }



    /**
     * 缓存
     */
    public function cache() {
        $this->tag_model->cache(isset($_GET['site']) && $_GET['site'] ? (int)$_GET['site'] : SITE_ID);
        (int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
    }

}