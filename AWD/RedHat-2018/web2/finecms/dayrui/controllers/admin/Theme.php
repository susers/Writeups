<?php


require FCPATH.'dayrui/core/M_File.php';

class Theme extends M_File {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->path = WEBPATH.'statics/';
		$this->template->assign(array(
			'path' => $this->path,
			'furi' => 'admin/theme/',
			'auth' => 'admin/theme/',
			'menu' => $this->get_menu(array(
				fc_lang('风格管理') => 'admin/theme/index'
			)),
		));
    }
	
}