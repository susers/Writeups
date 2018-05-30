<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Fcontent extends M_Controller {

    public $mid;
    public $form;
    protected $field;

    /**
     * 构造函数（网站表单）
     */
    public function __construct() {
        parent::__construct();
        $this->mid = $this->input->get('mid');
        $this->form = $this->get_cache('form-name-'.SITE_ID,  $this->mid);
        if (!$this->form) {
            $this->admin_msg(fc_lang('表单['.$this->mid.']不存在'));
        }
        $this->load->model('form_model');
        $this->field = array(
            'author' => array(
                'name' => fc_lang('录入作者'),
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'author',
                'setting' => array(
                    'option' => array(
                        'width' => 157,
                        'value' => $this->admin['username']
                    ),
                    'validate' => array(
                        'tips' => fc_lang('填写录入者的会员名称'),
                        'check' => '_check_member',
                        'required' => 1,
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
            'inputip' => array(
                'name' => fc_lang('客户端IP'),
                'ismain' => 1,
                'fieldname' => 'inputip',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                        'value' => $this->input->ip_address()
                    ),
                    'validate' => array(
                    )
                )
            )
        );
        $this->template->assign(array(
            'mid' => $this->mid,
            'menu' => $this->get_menu_v3(array(
                fc_lang($this->form['name']) => array('fcontent/index/mid/'.$this->mid, 'table'),
                fc_lang('添加') => array('fcontent/add/mid/'.$this->mid, 'plus'),
            )),
            'form' => 'form_'.$this->form['table'],
            'field' => $this->form['field'] + $this->field,
        ));
    }

    /**
     * 内容维护
     */
    public function index() {

        if (IS_POST && $this->input->post('action')) {
            $table = $this->form_model->prefix.'_'.$this->form['table'];
            if ($this->input->post('action') == 'del') {
                // 删除
                $this->load->model('attachment_model');
                $_ids = $this->input->post('ids');
                foreach ($_ids as $id) {
                    $row = $this->db->where('id', (int)$id)->get($table)->row_array();
                    if ($row) {
                        $this->db->where('id', (int)$id)->delete($table);
                        $this->db->where('id', (int)$id)->delete($table.'_data_'.(int)$row['tableid']);
                        $this->attachment_model->delete_for_table($table.'-'.$id);
                        $this->system_log('删除站点【#'.SITE_ID.'】表单【'.$this->form['table'].'】内容【#'.$id.'】'.$row['title']); // 记录日志
                    }
                }
            } elseif ($this->input->post('action') == 'order') {
                // 修改
                $_ids = $this->input->post('ids');
                $_data = $this->input->post('data');
                foreach ($_ids as $id) {
                    $this->db->where('id', (int)$id)->update($table, $_data[$id]);
                }
                $this->system_log('排序站点【#'.SITE_ID.'】表单【'.$this->form['table'].'】内容【#'.@implode(',', $_ids).'】'); // 记录日志
                unset($_ids, $_data);
            }
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        // 重置页数和统计
        IS_POST && $_GET['page'] = $_GET['total'] = 0;

        // 根据参数筛选结果
        $param = $this->input->get(NULL);
        unset($param['s'],$param['c'],$param['m'],$param['d'],$param['page']);
        if ($this->input->post('search')) {
            $search = $this->input->post('data');
            $param['keyword'] = $search['keyword'];
            $param['start'] = $search['start'];
            $param['end'] = $search['end'];
            $param['field'] = $search['field'];
        }

        // 数据库中分页查询
        list($data, $total)	= $this->form_model->limit_page(
            $this->form['table'],
            $param,
            max((int)$_GET['page'], 1),
            (int)$_GET['total']
        );
        $param['total'] = $total;

        $tpl = APPPATH.'templates/form_listc_'.$this->form['table'].'.html';
        $this->template->assign(array(
            'tpl' => str_replace(FCPATH, '/', $tpl),
            'list' => $data,
            'param'	=> $param,
            'total' => $total,
            'pages'	=> $this->get_pagination(dr_url('fcontent/index', $param), $param['total']),
        ));

        $this->template->display(is_file($tpl) ? basename($tpl) : 'form_listc.html');
    }

    /**
     * 添加内容
     */
    public function add() {

        if (IS_POST) {

            $data = $this->validate_filter($this->form['field'] + $this->field);

            // 验证出错信息
            if (isset($data['error'])) {
                $error = $data;
                $data = $this->input->post('data', TRUE);
            } else {
                // 设定文档默认值
                $data[1]['displayorder'] = 0;
                $data[1]['uid'] = $data[0]['uid'] = get_member_id($data[1]['author']);
                // 发布文档
                if (($id = $this->form_model->new_addc($this->form['table'], $data)) != FALSE) {
                    // 附件归档到文档
                    $this->attachment_handle($this->uid, $this->form_model->prefix.'_'.$this->form['table'].'-'.$id, $this->form['field']);
                    $this->system_log('添加站点【#'.SITE_ID.'】表单【'.$this->form['table'].'】内容【#'.$id.'】'); // 记录日志
                    $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_url('fcontent/index', array('mid'=>$this->mid)), 1);
                }
            }
            $data = $data[0] ? array_merge($data[1], $data[0]) : $data[1];
            unset($data['id']);
        }

        $tpl = APPPATH.'templates/form_addc_'.$this->form['table'].'.html';
        $this->template->assign(array(
            'tpl' => str_replace(FCPATH, '/', $tpl),
            'data' => $data,
            'error' => $error,
            'myfield' => $this->field_input($this->form['field'] + $this->field, $data)
        ));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'form_addc.html');
    }

    /**
     * 修改内容
     */
    public function edit() {


        $id = (int)$this->input->get('id');
        $table = $this->form_model->prefix.'_'.$this->form['table'];

        // 获取表单数据
        $data = $this->form_model->get_data($id, $table);
        !$data && $this->admin_msg(fc_lang('数据被删除或者查询不存在'));

        if (IS_POST) {
            $post = $this->validate_filter($this->form['field'] + $this->field);
            // 验证出错信息
            if (isset($post['error'])) {
                $error = $post;
                $data = $this->input->post('data', TRUE);
            } else {
                // 发布文档
                $post[1]['uid'] = $post[0]['uid'] = get_member_id($post[1]['author']);
                if ($this->form_model->new_editc($id, $this->form['table'], $data['tableid'], $post)) {
                    // 附件归档到文档
                    $this->attachment_handle($this->uid, $table.'-'.$id, $this->form['field']);
                    $this->system_log('修改站点【#'.SITE_ID.'】表单【'.$this->form['table'].'】内容【#'.$id.'】'); // 记录日志
                    $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_url('fcontent/index', array('mid'=>$this->mid)), 1);
                }
            }
            $data = $post[0] ? array_merge($post[1], $post[0]) : $post[1];
            unset($data['id']);
        }

        $tpl = APPPATH.'templates/form_addc_'.$this->form['table'].'.html';
        $this->template->assign(array(
            'tpl' => str_replace(FCPATH, '/', $tpl),
            'data' => $data,
            'error' => $error,
            'myfield' => $this->field_input($this->form['field'] + $this->field, $data)
        ));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'form_addc.html');
    }




}