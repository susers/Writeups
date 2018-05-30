<?php


/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */


class Form extends M_Controller {

	public $form;
    protected $field;
    protected $uriprefix;
	
    /**
     * 构造函数（网站表单）
     */
    public function __construct() {
        parent::__construct();
        $this->mid = dr_safe_replace($this->input->get('mid', true));
        $this->form = $this->get_cache('form-name-'.SITE_ID,  $this->mid);
        if (!$this->form) {
            $this->admin_msg(fc_lang('表单['.$this->mid.']不存在'));
        }
        $this->load->model('form_model');
    }
	

	/**
     * 提交内容
     */
    public function index() {

		!$this->form['setting']['post'] && (IS_POST ? exit($this->call_msg(fc_lang('此表单没有开启前端提交功能'))) : $this->msg(fc_lang('此表单没有开启前端提交功能')));

		if (IS_POST) {

			$this->form['setting']['code'] && !$this->check_captcha('code') && exit($this->call_msg(fc_lang('验证码不正确')));
            
			$data = $this->validate_filter($this->form['field']);
				
			// 验证出错信息
			isset($data['error']) && exit($this->call_msg($data['msg']));

            $data[1]['uid'] =$data[0]['uid'] = $this->uid;
			$data[1]['author'] = $this->uid ? $this->member['username'] : 'guest';
			$data[1]['inputip'] = $this->input->ip_address();
			$data[1]['inputtime'] = SYS_TIME;
			$data[1]['displayorder'] = 0;
			
			$this->load->model('form_model');
			$data[1]['id'] = $id = $this->form_model->new_addc($this->form['table'], $data);
			
			if ($this->form['setting']['send'] && $this->form['setting']['template']) {
                    $rep = new php5replace($data[1]+$data[0]);
					$content = preg_replace_callback('#{(.*)}#U', array($rep, 'php55_replace_data'), $this->form['setting']['template']);
                    $content = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $content);
                    unset($rep);

				$this->sendmail($this->form['setting']['send'], fc_lang('【%s】通知 [来自：'.SITE_NAME.']', $this->form['name']), nl2br($content));
			}
			$this->call_msg(fc_lang('操作成功'), 1, $data);
		} else {
            $tpl = dr_tpl_path('form_'.$this->form['table'].'.html');
            $cat = array();
            $catid = 0;
            foreach ($this->get_cache('category-'.SITE_ID) as $t) {
                if ($t['tid'] == 2) {
                    $url = trim(str_replace(SITE_URL, '', $t['setting']['linkurl']), '/');
                    $now = trim(str_replace(SITE_URL, '', FC_NOW_URL), '/');
                    if ($url && $url == $now) {
                        $cat = $t;
                        $catid = $t['id'];
                    }
                }
            }
			$this->template->assign(array(
			    'cag' => $cat,
			    'catid' => $catid,
				'form' => $this->form,
				'code' => $this->form['setting']['code'],
				'myfield' => $this->field_input($this->form['field']),
				'meta_title' => $this->form['name'].SITE_SEOJOIN.SITE_NAME
			));
			$this->template->display(is_file($tpl) ? basename($tpl) : 'form.html');
		}
    }
	
	/**
     * 回调方法 有问题
     */
	public function call_msg($msg, $code = 0, $data = array()) {


        $url = $this->form['setting']['rt_url'];
		!$url && $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		
		if (IS_AJAX) {
			exit(dr_json($code, $msg, $url)); // AJAX请求时返回json格式
		} else {
			if ($code) {
				$this->msg($msg, $url, 1); // 成功
			} else {
				$this->msg($msg); // 错误
			}
		}
	}
	
}