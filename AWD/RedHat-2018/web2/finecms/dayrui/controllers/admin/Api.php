<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Api extends M_Controller {


    public function menu() {

        $url = urldecode(dr_safe_replace($this->input->get('v')));
        $arr = parse_url($url);
        $queryParts = explode('&', $arr['query']);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        // 基础uri
        $uri = ($params['s'] ? $params['s'].'/' : '').'admin/'.($params['c'] ? $params['c'] : 'home').'/'.($params['m'] ? $params['m'] : 'index');
        // 查询名称
        $menu = $this->db->select('name')->like('uri', $uri)->get('admin_menu')->row_array();
        $name = $menu ? $menu['name'] : '未知名称';
        // 替换URL

        $admin = $this->db->where('uid', $this->uid)->get('admin')->row_array();
        if ($admin) {
            $menu = dr_string2array($admin['usermenu']);
            foreach ($menu as $t) {
                $t['url'] == $url && exit('已经存在');
            }
            $menu[] = array(
                'name' => $name,
                'url' => $url,
            );
            $this->db->where('uid', $this->uid)->update(
                'admin', array(
                    'usermenu' => dr_array2string($menu)
                )
            );
            exit();
        }
        exit('稍后再试');

    }


    /**
     * ajax文件上传
     *
     * @return void
     */
	public function ajax_upload() {
		

	}
	
	
    /**
     * 文件上传
     *
     * @return void
     */
    public function upload() {
        $ext = 'jpg,gif,png,js,css,html';
        $this->template->assign(array(
            'ext' => str_replace(',', '|', $ext),
            'page' => 0,
            'size' => 1024 * 1024,
            'path' => $this->input->get('path'),
            'types' => '*.'.str_replace(',', ';*.', $ext),
            'fcount' => 50,
            'is_admin' => 1,
        ));
        $this->template->display('upload.html');
    }

    /**
     * 文件上传处理
     *
     * @return void
     */
    public function swfupload() {

        if (IS_POST) {
            $ext = 'jpg,gif,png,js,css,html';
            $path = $this->input->post('path');
            !is_dir($path) && exit('0,目录（'.$path.'）不存在');
            $this->load->library('upload', array(
                'max_size' => 1024 * 1024,
                'overwrite' => TRUE,
                'file_name' => '',
                'upload_path' => $path,
                'allowed_types' => str_replace(',', '|', $ext),
                'file_ext_tolower' => TRUE,
            ));
            if ($this->upload->do_upload('Filedata')) {
                $info = $this->upload->data();
                $_ext = str_replace('.', '', $info['file_ext']);
                $file = str_replace(WEBPATH, '', $info['full_path']);
                !is_file(WEBPATH.$file) && $file = THEME_PATH.'admin/images/ext/blank.gif';
                $icon = is_file(THEME_PATH.'admin/images/ext/'.$_ext.'.gif') ? THEME_PATH.'admin/images/ext/'.$_ext.'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
                //唯一ID,文件全路径,图标,文件名称,文件大小,扩展名
                exit('1,'.$file.','.$icon.','.str_replace(array('|', '.'.$_ext), '', $info['client_name']).','.dr_format_file_size($info['file_size'] * 1024).','.$_ext);
            } else {
                exit('0,'.$this->upload->display_errors('', ''));
            }
        }
    }
	
	/**
     * 查看资料
     */
	public function member() {

        $uid = str_replace('author_', '', $this->input->get('uid'));
        ($uid == 'guest' || !$uid) && exit('<div style="padding-top:50px;color:blue;font-size:14px;text-align:center">'.fc_lang('游客').'</div>');
        
        $data = is_numeric($uid) ? $this->db->where('uid', (int)$uid)->limit(1)->get('member')->row_array() : $this->db->where('username', $uid)->limit(1)->get('member')->row_array();

        !$data && exit('(#'.$uid.')'.fc_lang('对不起，该会员不存在！'));

        $this->load->library('dip');
        $data['address'] = $this->dip->address($data['regip']);

		$this->template->assign(array(
			'data' => $data,
		));
		$this->template->display('member.html');
	}
	

}