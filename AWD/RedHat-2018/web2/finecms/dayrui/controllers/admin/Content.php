<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Content extends M_Controller {

    public $mid;
    public $module;
    public $field;
    public $sysfield;

    public function __construct() {
        parent::__construct();
        $this->load->library('Dfield');
        $this->sysfield = array(
            'author' => array(
                'name' => fc_lang('录入作者'),
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'author',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                        'value' => $this->admin['username']
                    ),
                    'validate' => array(
                        'tips' => fc_lang('填写录入者的会员名称'),
                        'check' => '_check_member',
                        'required' => 1,
                        'formattr' => ' ondblclick="dr_dialog_member(\'author\')" ',
                    )
                )
            ),
            'inputtime' => array(
                'name' => fc_lang('录入时间'),
                'ismain' => 1,
                'fieldtype' => 'Date',
                'fieldname' => 'inputtime',
                'setting' => array(
                    'option' => array(
                        'width' => 200
                    ),
                    'validate' => array(
                        'required' => 1,
                        'formattr' => '',
                    )
                )
            ),
            'updatetime' => array(
                'name' => fc_lang('更新时间'),
                'ismain' => 1,
                'fieldtype' => 'Date',
                'fieldname' => 'updatetime',
                'setting' => array(
                    'option' => array(
                        'width' => 200
                    ),
                    'validate' => array(
                        'required' => 1,
                        'formattr' => '',
                    )
                )
            ),
            'inputip' => array(
                'name' => fc_lang('客户端IP'),
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'inputip',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                        'value' => $this->input->ip_address()
                    ),
                    'validate' => array(
                        'formattr' => ' ondblclick="dr_dialog_ip(\'inputip\')" ',
                    )
                )
            ),
            'hits' => array(
                'name' => fc_lang('阅读量'),
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'hits',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                        'value' => 0,
                    )
                )
            ),
            'status' => array(
                'name' => fc_lang('状态'),
                'ismain' => 1,
                'fieldname' => 'status',
                'fieldtype' => 'Radio',
                'setting' => array(
                    'option' => array(
                        'value' => 9,
                        'options' => fc_lang('正常').'|9'.chr(13).fc_lang('关闭').'|10'
                    ),
                    'validate' => array(
                        'tips' => fc_lang('关闭状态起内容暂存作用，除自己和管理员以外的人均无法访问'),
                    )
                )
            ),
        );
        $this->mid = $this->input->get('mid');
        $this->module = $this->get_cache('module', $this->mid);
        if (!$this->module) {
            $this->admin_msg(fc_lang('模型不存在'));
        }
        $this->load->model('content_model');
        $this->content_model->init($this->module);
    }

    public function _get_field($catid = 0) {
        return $this->module['field'];
    }

    public function index() {

        if (IS_POST && !$this->input->post('search')) {
            $ids = $this->input->post('ids');
            $action = $this->input->post('action');
            !$ids && ($action == 'html' ? $this->admin_msg(fc_lang('您还没有选择呢')) : exit(dr_json(0, fc_lang('您还没有选择呢'))));
            switch ($action) {
                case 'del':
                    $ok = $no = 0;
                    foreach ($ids as $id) {
                        $data = $this->db->where('id', (int)$id)->select('id,catid,tableid')->get($this->content_model->prefix)->row_array();
                        if ($data) {
                            $ok++;
                            $this->content_model->delete_for_id((int)$data['id'], (int)$data['tableid']);
                        }
                    }
                    $this->system_log('删除站点【#'.SITE_ID.'】模型【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】'); // 记录日志
                    exit(dr_json($no ? 0 : 1, $no ? fc_lang('管理员：%s', $ok, $no) : fc_lang('操作成功，正在刷新...')));
                    break;
                case 'order':
                    $_data = $this->input->post('data');
                    foreach ($ids as $id) {
                        $this->db->where('id', $id)->update($this->content_model->prefix, $_data[$id]);
                    }
                    $this->system_log('排序站点【#'.SITE_ID.'】模型【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】'); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                    break;
                case 'move':
                    $catid = $this->input->post('catid');
                    if (!$catid) {
                        exit(dr_json(0, fc_lang('目标栏目id不存在')));
                    }
                    $this->content_model->move($ids, $catid);
                    $this->system_log('站点【#'.SITE_ID.'】模型【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】更改栏目#'.$catid); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                    break;
                default :
                    exit(dr_json(0, fc_lang('操作成功，正在刷新...')));
                    break;
            }
        }

        // 重置页数和统计
        IS_POST && $_GET['page'] = $_GET['total'] = 0;
        IS_POST && $_GET['caitd'] = $_POST['data']['caitd'];
        // 筛选结果
        $param = $this->input->get(NULL, TRUE);
        $catid = isset($param['catid']) ? (int)$param['catid'] : 0;
        unset($param['s'], $param['c'], $param['m'], $param['d'], $param['page']);

        // 按字段的搜索
        $this->field = $this->module['field'];
        $this->field['author'] = array('name' => fc_lang('录入作者'), 'ismain' => 1, 'fieldname' => 'author');

        // 数据库中分页查询
        list($list, $param) = $this->content_model->limit_page($param, max((int)$_GET['page'], 1), (int)$_GET['total']);
        $param['mid'] = $this->mid;

        $this->template->assign('menu', $this->get_menu_v3(array(
            fc_lang('内容管理') => array('admin/content/index/mid/'.$this->mid, 'th-large'),
            fc_lang('发布内容') => array('admin/content/add/mid/'.$this->mid, 'plus'),
            fc_lang('更新URL地址') => array('admin/content/url/mid/'.$this->mid, 'link'),
        )));

        // 存储当前页URL
        $this->_set_back_url('content/index', $param);

        // 插件判断
        $clink = array();
        $local = $this->local_app();
        if ($local) {
            foreach ($local as $t) {
                if ($t['clink']) {
                    $clink[] = $t['clink'];
                }
            }
        }

        $this->template->assign(array(
            'mid' => $this->mid,
            'list' => $list,
            'clink' => $clink,
            'param' => $param,
            'field' => $this->field,
            'pages' => $this->get_pagination(dr_url('content/index', $param), $param['total']),
            'select' => $this->select_category($this->get_cache('category-'.SITE_ID), 0, 'id=\'move_id\' name=\'catid\'', ' --- ', 1, 1),
            'select2' => $this->select_category($this->get_cache('category-'.SITE_ID), (int)$param['catid'] , ' name=\'data[catid]\'', ' --- ', 0, 1),
            'html_url' => 'index.php?',
            'post_url' => $this->duri->uri2url('admin/content/add/mid/'.$this->mid),
            'list_url' =>  $this->duri->uri2url('admin/content/index/mid/'.$this->mid),
        ));
        $this->template->display('content_index.html');
    }

    public function add() {

        $catid = (int)$this->input->get('catid');

        $error = $data = array();
        $category = $this->get_cache('category-'.SITE_ID);


        // 提交保存操作------
        if (IS_POST) {
            $cid = (int)$this->input->post('catid');
            $catid = $cid;
            $cat = $category[$catid];
            // 设置uid便于校验处理
            $uid = $this->input->post('data[author]') ? get_member_id($this->input->post('data[author]')) : 0;
            $_POST['data']['id'] = 0;
            $_POST['data']['uid'] = $uid;
            // 获取字段
            $myfield = array_merge($this->_get_field(), $this->sysfield);
            $data = $this->validate_filter($myfield);
            // 返回错误
            if (isset($data['error'])) {
                $error = $data;
                $data = $this->input->post('data', TRUE);
            } elseif (!$catid) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('还没有选择栏目'));
            } else {
                $data[1]['uid'] = $uid;
                $data[1]['catid'] = $catid;

                // 正常发布
                if (($id = $this->content_model->add($data)) != FALSE) {
                    // 执行提交后的脚本
                    $this->validate_table($id, $myfield, $data);
                    $this->system_log('添加 站点【#'.SITE_ID.'】模型【'.$this->mid.'】内容【#'.$id.'】'); // 记录日志
                    if ($this->input->post('action') == 'back') {
                        $this->admin_msg(
                            fc_lang('操作成功，正在刷新...'),
                            $this->_get_back_url('content/index', array('mid' => $this->mid)),
                            1,
                            1
                        );
                    } else {
                        $error = array('msg' => dr_lang('发布成功'), 'status'=>1);
                    }
                }
            }
            $select = $this->select_category($category, $catid, 'id=\'dr_catid\' name=\'catid\'', '', 1, 1);
        } else {
            if (!$catid) {
                list($select, $catid) = $this->select_category($category, 0, 'id=\'dr_catid\' name=\'catid\'', '', 1, 1, 1);
            } else {
                $select = $this->select_category($category, $catid, 'id=\'dr_catid\' name=\'catid\'', '', 1, 1);
            }
        }

        $myfield = $this->_get_field();

        $this->template->assign(array(
            'data' => $data,
            'menu' => $this->get_menu_v3(array(
                fc_lang('返回') => array('admin/content/index/mid/'.$this->mid, 'reply'),
                fc_lang('发布') => array('/admin/content/add/mid/'.$this->mid, 'plus')
            )),
            'catid' => $catid,
            'error' => $error,
            'select' => $select,
            'myfield' => $this->new_field_input($myfield, $data, TRUE),
            'sysfield' => $this->new_field_input($this->sysfield, $data, TRUE, '', '<div class="form-group" id="dr_row_{name}"><label class="col-sm-12">{text}</label><div class="col-sm-12">{value}</div></div>'),

        ));
        $this->template->display('content_add.html');
    }

    /**
     * 修改
     */
    public function edit() {

        $id = (int)$this->input->get('id');
        $cid = (int)$this->input->get('catid');
        $data = $this->content_model->get($id);
        $catid = $cid ? $cid : $data['catid'];
        $error = $myflag = array();
        unset($cid);

        // 数据判断
        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));


        // 栏目缓存
        $category = $this->get_cache('category-'.SITE_ID);


        if (IS_POST) {
            $cid = (int)$this->input->post('catid');
            $catid = $cid;
            unset($cid);
            // 设置uid便于校验处理
            $uid = $this->input->post('data[author]') ? get_member_id($this->input->post('data[author]')) : 0;
            $_POST['data']['id'] = $id;
            $_POST['data']['uid'] = $uid;
            // 获取字段
            $myfield = array_merge($this->_get_field(), $this->sysfield);
            $post = $this->validate_filter($myfield, $data);
            if (isset($post['error'])) {
                $error = $post;
            } elseif (!$catid) {
                $error = array('error' => 'catid', 'msg' => fc_lang('还没有选择栏目'));
            } else {
                $post[1]['uid'] = $uid;
                $post[1]['catid'] = $catid;
                $post[1]['updatetime'] = $this->input->post('no_time') ? $data['updatetime'] : $post[1]['updatetime'];

                // 正常保存
                $this->content_model->edit($data, $post);
                // 执行提交后的脚本
                $this->validate_table($id, $myfield, $post);
               // 操作成功处理附件
                $this->attachment_handle($post[1]['uid'], $this->content_model->prefix.'-'.$id, $myfield, $data);

                $this->system_log('修改 站点【#'.SITE_ID.'】模型 内容【#'.$id.'】'); // 记录日志

                //exit;
                $this->admin_msg(
                    fc_lang('操作成功，正在刷新...'),
                    $this->_get_back_url('content/index', array('mid' => $this->mid)),
                    1,
                    1
                );
            }
            $data = $this->input->post('data', TRUE);
        } else {

        }


        // 可用字段
        $myfield = $this->_get_field($catid);

        $data['updatetime'] = SYS_TIME;
        $this->template->assign(array(
            'data' => $data,
            'menu' => $this->get_menu_v3(array(
                fc_lang('返回') => array('admin/content/index/mid/'.$this->mid, 'reply'),
                fc_lang('发布') => array('/admin/content/add/mid/'.$this->mid, 'plus')
            )),
            'catid' => $catid,
            'error' => $error,
            'select' => $this->select_category($category, $catid, 'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1),
            'myfield' => $this->new_field_input($myfield, $data, TRUE),
            'sysfield' => $this->new_field_input($this->sysfield, $data, TRUE, '', '<div class="form-group" id="dr_row_{name}"><label class="col-sm-12">{text}</label><div class="col-sm-12">{value}</div></div>'),

        ));
        $this->template->display('content_add.html');
    }



    // 文档状态设定
    public function status() {

        $id = (int)$this->input->get('id');
        $data = $this->content_model->get($id);
        !$data && exit(dr_json(0, fc_lang('对不起，数据被删除或者查询不存在')));

        // 删除缓存
        $this->clear_cache('show'.APP_DIR.SITE_ID.$id);
        $this->clear_cache('mshow'.APP_DIR.SITE_ID.$id);

        if ($data['status'] == 10) {
            $this->db->where('id', $id)->update($this->content_model->prefix, array('status' => 9));
            $this->db->where('id', $id)->update(SITE_ID.'_index', array('status' => 9));
            // 调用方法状态更改方法
            $data['status'] = 9;
            $this->content_model->_update_status($data);
            $this->system_log('修改 站点【#'.SITE_ID.'】模型【'.APP_DIR.'】内容【#'.$id.'】状态为【正常】'); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $data['catid']));
        } else {

            $this->db->where('id', $id)->update($this->content_model->prefix, array('status' => 10));
            $this->db->where('id', $id)->update(SITE_ID.'_index', array('status' => 10));
            // 调用方法状态更改方法
            $data['status'] = 10;
            $this->content_model->_update_status($data);
            $this->system_log('修改 站点【#'.SITE_ID.'】模型【'.APP_DIR.'】内容【#'.$id.'】状态为【关闭】'); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...'), 0));
        }

    }



    /**
     * 更新URL 兼容处理
     */
    public function url() {

        $cfile = SITE_ID.APP_DIR.$this->uid.$this->input->ip_address().'_content_url';

        // 处理url
        if ($this->input->get('todo')) {
            $page = max(1, (int)$this->input->get('page'));
            $psize = 100; // 每页处理的数量
            $cache = $this->cache->file->get($cfile);
            if ($cache) {
                $total = $cache['total'];
                $catid = $cache['catid'];
            } else {
                $catid = 0;
                $total = $this->db->count_all_results($this->content_model->prefix);
            }
            $tpage = ceil($total / $psize); // 总页数
            if ($page > $tpage) {
                // 更新完成删除缓存
                $this->cache->file->delete($cfile);
                $this->admin_msg(fc_lang('更新成功'), NULL, 1);
            }
            $table = $this->content_model->prefix;
            $catid && $this->db->where_in('catid', $catid);
            $data = $this->db->limit($psize, $psize * ($page - 1))->order_by('id DESC')->get($table)->result_array();
            foreach ($data as $t) {
                $url = dr_show_url($t);
                $this->db->update($table, array('url' => $url), 'id='.$t['id']);
            }
            $this->admin_msg(fc_lang('正在执行中(%s) ... ', "$tpage/$page"), dr_url('content/url', array('mid' => $this->mid, 'todo' => 1, 'page' => $page + 1)), 2, 0);
        } else {
            // 统计数量
            $total = $this->db->count_all_results($this->content_model->prefix);
            $this->cache->file->save($cfile, array('catid' => 0, 'total' => $total), 10000);
            if ($total) {
                $this->system_log('站点【#'.SITE_ID.'】模型【'.APP_DIR.'】更新URL地址#'.$total); // 记录日志
                $this->admin_msg(fc_lang('可更新内容%s条，正在准备执行...', $total), dr_url('content/url', array('mid' => $this->mid, 'todo' => 1)), 2);
            } else {
                $this->admin_msg(fc_lang('抱歉，没有找到可更新的内容'));
            }
        }
    }

}