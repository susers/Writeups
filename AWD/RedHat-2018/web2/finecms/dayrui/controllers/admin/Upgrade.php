<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Upgrade extends M_Controller {

    /**
     * 程序管理
     */
    public function index() {

        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('程序升级') => array('admin/upgrade/index', 'refresh'),
            )),
        ));
        $this->template->display('upgrande.html');
    }

// 版本列表
    public function vlist() {

        $data = dr_catcher_data('http://www.poscms.net/version.php?cms=finecms');

        if (!$data) {
            exit('<p style="color:red;"> 暂时无法获取到服务器端版本信息 </p>');
        }

        if (strlen($data) > 20) {
            exit('<p style="color:red;"> 返回数据不规范，请联系官方！ </p>');
        }

        $fwq = (string)(date('Y.m.d', strtotime($data)));
        $azb = (string)(DR_UPDATE);
        if ($fwq != $azb) {
            $data.= '，'.'<span style="color:red">有新版本可更新</span>';

        }


        echo('<p> 本网站程序更新时间为： '.DR_UPDATE.'</a></p>');
        exit('<p> <a href="https://gitee.com/dayrui/finecms/" style="color:green;" target="_blank">服务器程序更新时间为： '.$data.'</a></p>');


    }


}