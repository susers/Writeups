<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



require FCPATH.'dayrui/core/M_File.php';

class Tpl extends M_File {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->path = $this->router->method == 'mobile' || $this->input->get('ismb') ? TPLPATH.'mobile/' : TPLPATH.'pc/';
		$this->template->assign(array(
			'path' => $this->path,
			'furi' => 'admin/tpl/',
			'auth' => 'admin/tpl/',
			'menu' => $this->get_menu(array(
				fc_lang('模板管理') => 'admin/tpl/index',
				fc_lang('移动端模板') => 'admin/tpl/mobile',
			)),
            'ismb' => $this->router->method == 'mobile' ? 1 : 0,
		));
    }
	
}