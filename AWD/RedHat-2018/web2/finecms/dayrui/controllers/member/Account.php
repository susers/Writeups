<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */


class Account extends M_Controller {



    /**
     * OAuth解绑
     */
    public function jie() {

        $id = dr_safe_replace($this->input->get('id'));
        if ($this->get_cache('member', 'setting', 'regoauth')) {
            $this->msg(fc_lang('您的账号不支持解除绑定'));
        } elseif (!$this->member['username'] && !$this->member['password']) {
            $this->msg(fc_lang('操作成功，正在刷新...'));
        }

        $this->db->where('uid', $this->uid)->where('oauth', $id)->delete('member_oauth');

        // 解绑积分处理
        !$this->db
            ->where('uid', $this->uid)
            ->where('type', 0)
            ->where('mark', 'jie_'.$id)
            ->count_all_results('member_scorelog') && $this->member_model->update_score(0, $this->uid, (int)$this->member_rule['jie_experience'], 'jie_'.$id, 'OAuth账号解绑');
        
        // 解绑虚拟币处理
        !$this->db
            ->where('uid', $this->uid)
            ->where('type', 1)
            ->where('mark', 'jie_'.id)
            ->count_all_results('member_scorelog') && $this->member_model->update_score(1, $this->uid, (int)$this->member_rule['jie_score'], 'jie_'.$id, 'OAuth账号解绑');
        
        $this->msg(fc_lang('操作成功，正在刷新...'), dr_member_url('account/oauth'), 1, 3);
    }

    /**
     * OAuth绑定
     */
    public function bang() {

        $appid = dr_safe_replace($this->input->get('id'));
        $oauth = require WEBPATH.'config/oauth.php';
        $config	= $oauth[$appid];
        !$config && $this->msg(fc_lang('OAuth错误: 缺少OAuth参数'));

        $config['url'] = SITE_URL.'index.php?s=member&c=account&m=bang&id='.$appid; // 回调地址设置
        $this->load->library('OAuth2');

        // OAuth
        $code = $this->input->get('code', TRUE);
        $oauth = $this->oauth2->provider($appid, $config);

        if (!$code) { // 登录授权页
            try {
                $oauth->authorize();
            } catch (OAuth2_Exception $e) {
                $this->msg(fc_lang('OAuth授权错误').' - '.$e);
            }
        } else { // 回调返回数据
            try {
                $user = $oauth->get_user_info($oauth->access($code));
                if (is_array($user) && $user['oid']) {
                    if ($uid = $this->member_model->OAuth_bang($appid, $user)) {
                        $this->msg(fc_lang('抱歉！该授权已经被绑定过了，<a href="%s" target="_blank">看看Ta是谁？</a>', dr_space_url($uid)));
                    } else {
                        // 绑定积分处理
                        !$this->db
                            ->where('uid', $this->uid)
                            ->where('type', 0)
                            ->where('mark', 'bang_'.$appid)
                            ->count_all_results('member_scorelog') && $this->member_model->update_score(0, $this->uid, (int)$this->member_rule['bang_experience'], 'bang_'.$appid, 'OAuth账号绑定');
                        
                        // 绑定虚拟币处理
                        !$this->db
                            ->where('uid', $this->uid)
                            ->where('type', 1)
                            ->where('mark', 'bang_'.$appid)
                            ->count_all_results('member_scorelog') && $this->member_model->update_score(1, $this->uid, (int)$this->member_rule['bang_score'], 'bang_'.$appid, 'OAuth账号绑定');
                        $this->msg(fc_lang('绑定成功'), dr_member_url('account/oauth'), 1, 3);
                    }
                } else {
                    $this->msg(fc_lang('OAuth回调错误: 获取用户信息失败'));
                }
            } catch (OAuth2_Exception $e) {
                $this->msg(fc_lang('OAuth回调错误: 获取用户信息失败').' - '.$e);
            }
        }
    }


    /**
     * OAuth
     */
    public function oauth() {
        $this->template->assign(array(
            'list' => $this->member['oauth'],
        ));
        $this->template->display('account_oauth.html');
    }

    /**
     * 修改密码
     */
    public function password() {

        $error = 0;

        if (IS_POST) {

            $password = dr_safe_replace($this->input->post('password'));
            $password1 = dr_safe_replace($this->input->post('password1'));
            $password2 = dr_safe_replace($this->input->post('password2'));

            if (!$password1 || $password1 != $password2) {
                $error = fc_lang('两次密码输入不一致');
            } elseif ($password == $password2) {
                $error = fc_lang('不能与原密码相同');
            } elseif (md5(md5($password).$this->member['salt'].md5($password)) != $this->member['password']) {
                $error = fc_lang('当前密码不正确');
            } else {
                if (defined('UCSSO_API')) {
                    $rt = ucsso_edit_password($this->uid, $password1);
                    // 修改失败
                    if (!$rt['code']) {
                        $this->admin_msg(fc_lang($rt['msg']));
                    }
                } elseif ($this->get_cache('MEMBER', 'setting', 'ucenter')) {
                    $ucresult = uc_user_edit($this->member['username'], $password, $password1, $this->member['email']);
                    $ucresult == -1 && $error = fc_lang('旧密码不正确');
                }
            }

            if ($error === 0) {
                $this->db->where('uid', $this->uid)->update('member', array(
                    'password' => md5(md5($password1).$this->member['salt'].md5($password1))
                ));
                $this->member_msg(fc_lang('密码修改成功'), dr_member_url('account/password'), 1);
            }

        }

        $this->template->assign(array(
            'result_error' => $error
        ));
        $this->template->display('password.html');
    }

    /**
     * 密码校验
     */
    public function cpassword() {
        $password = dr_safe_replace($this->input->post('password'));
        echo md5(md5($password).$this->member['salt'].md5($password)) == $this->member['password'] ? '' : fc_lang('旧密码不正确');
    }

    /**
     * 上传头像
     */
    public function avatar() {
        $this->template->display('avatar.html');
    }

    /**
     *  上传头像处理
     *  传入头像压缩包，解压到指定文件夹后删除非图片文件
     */
    public function upload() {

        // 创建图片存储文件夹
        $dir = dr_upload_temp_path().'member/'.$this->uid.'/';
        @dr_dir_delete($dir);
        !is_dir($dir) && dr_mkdirs($dir);

        if ($_POST['tx']) {
            $file = str_replace(' ', '+', $_POST['tx']);
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $file, $result)){
                $new_file = $dir.'0x0.'.$result[2];
                if (!in_array(strtolower($result[2]), array('jpg', 'jpeg', 'png', 'gif'))) {
                    exit(dr_json(0, '目录权限不足'));
                }
                if (!@file_put_contents($new_file, base64_decode(str_replace($result[1], '', $file)))) {
                    exit(dr_json(0, '目录权限不足'));
                } else {
                    list($width, $height, $type, $attr) = getimagesize($new_file);
                    if (!$type) {
                        @unlink($new_file);
                        exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '图片字符串不规范') : 'error3');
                    }
                    $this->load->library('image_lib');
                    $config['create_thumb'] = TRUE;
                    $config['thumb_marker'] = '';
                    $config['maintain_ratio'] = FALSE;
                    $config['source_image'] = $new_file;
                    foreach (array(30, 45, 90, 180) as $a) {
                        $config['width'] = $config['height'] = $a;
                        $config['new_image'] = $dir.$a.'x'.$a.'.'.$result[2];
                        $this->image_lib->initialize($config);
                        if (!$this->image_lib->resize()) {
                            exit(dr_json(0, '上传错误：'.$this->image_lib->display_errors()));
                            break;
                        }
                    }

                    // ok
                    $my = SYS_UPLOAD_PATH.'/member/'.$this->uid.'/';
                    @dr_dir_delete($my);
                    !is_dir($my) && dr_mkdirs($my);

                    $c = 0;
                    if ($fp = @opendir($dir)) {
                        while (FALSE !== ($file = readdir($fp))) {
                            $ext = substr(strrchr($file, '.'), 1);
                            if (in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {
                                if (copy($dir.$file, $my.$file)) {
                                    $c++;
                                }
                            }
                        }
                        closedir($fp);
                    }
                    if (!$c) {
                        exit(dr_json(0,  fc_lang('未找到目录中的图片')));
                    }
                }
            } else {
                exit(dr_json(0, '图片字符串不规范'));
            }
        } else {
            exit(dr_json(0, '图片不存在'));
        }

// 上传图片到服务器
        if (defined('UCSSO_API')) {
            $rt = ucsso_avatar($this->uid, file_get_contents($dir.'90x90.jpg'));
            !$rt['code'] && $this->_json(0, fc_lang('通信失败：%s', $rt['msg']));
        }


        exit('1');
    }

}