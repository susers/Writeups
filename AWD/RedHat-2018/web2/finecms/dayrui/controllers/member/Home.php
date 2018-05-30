<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Home extends M_Controller {


    /**
     * 首页
     */
    public function index() {

        $MEMBER = $this->get_cache('member');
        $error = NULL;
        $field = array(
            'name' => array(
                'name' => fc_lang('姓名'),
                'ismain' => 0,
                'ismember' => 1,
                'fieldname' => 'name',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                    ),
                    'validate' => array(
                        'xss' => 1,
                        'required' => 0,
                    )
                )
            ),
            'phone' => array(
                'name' => fc_lang('手机号码'),
                'ismain' => 0,
                'ismember' => 1,
                'fieldname' => 'phone',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                    ),
                    'validate' => array(
                        'xss' => 1,
                        'required' => 0,
                    )
                )
            ),
        );




        // 可用字段
        if ($MEMBER['field']) {
            foreach ($MEMBER['field'] as $t) {
                 $field[] = $t;
            }
        }

        if (IS_POST) {

            $data = $this->validate_filter($field, $this->member);

            if (isset($data['error'])) {
                $error = $data;
                (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
                $data = $this->input->post('data', TRUE);
            } else {
                $this->member_model->edit($data[0], $data[1]);
                $this->attachment_handle($this->uid, $this->db->dbprefix('member') . '-' . $this->uid, $field, $this->member);

                $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('home/index'), 1);
            }

        } else {
            $data = $this->member;
        }

        $this->template->assign(array(
            'data' => $data,
            'field' => $field,
            'myfield' => $this->field_input($field, $data, FALSE, 'uid'),
            'result_error' => $error
        ));
        $this->template->display('index.html');

    }
	



}