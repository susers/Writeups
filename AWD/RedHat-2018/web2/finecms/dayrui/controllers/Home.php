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

        $this->template->assign(array(
            'indexc' => 1,
            'meta_title' => SITE_TITLE,
            'meta_keywords' => SITE_KEYWORDS,
            'meta_description' => SITE_DESCRIPTION,
        ));
        $this->template->display('index.html');
    }

}