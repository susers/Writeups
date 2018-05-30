<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */
class Weixin extends M_Controller {

    private $wx;
    private $token;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->output->enable_profiler(FALSE);
        $this->load->model('weixin_model');
        $this->wx = $this->get_cache('weixin-'.SITE_ID);
    }

    /**
     * 微信交互控制器
     */
    public function index() {

        // 删除微信传递的token干扰
        unset ( $_REQUEST ['token'] );

        $token = $this->wx['config']['token'];
        $nonce = $this->input->get('nonce');
        $signature = $this->input->get('signature');
        $timestamp = $this->input->get('timestamp');

        // 处理参数
        $tmp_arr = array($token, $timestamp, $nonce);
        sort($tmp_arr);
        $tmp_str = implode($tmp_arr);
        $tmp_str = sha1($tmp_str);

        if ($tmp_str == $signature) {
            // 判读是不是只是验证
            $echostr = $this->input->get('echostr', true);
            if (!empty($echostr)) {
                echo $echostr;exit;
            } else {
                // 实际处理用户消息
                $content = file_get_contents('php://input');
                //file_put_contents(FCPATH."wx.txt", var_export($content, true));
                if ($content) {
                    // 解析微信传过来的 XML 内容 转化为数组
                    $this->data = dr_object2array(simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA));
                    $this->token = $this->data['ToUserName'];
                    // 初始化用户 将其加入到粉丝组里面
                    $this->_init_user();
                    // 回复数 据
                    //file_put_contents(FCPATH."wx.txt", var_export($data, true));
                }
            }
        }
        exit;
    }


    // 会员绑定与注册
    public function member() {

        $app = $this->input->get('state');
        $code = $this->input->get('code');
        $data = json_decode(dr_catcher_data('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx['config']['key'].'&secret='.$this->wx['config']['secret'].'&code='.$code.'&grant_type=authorization_code'), true);
        if (isset($data['errcode']) && $data['errcode']) {
            $this->admin_msg('错误代码：'.$data['errcode']);
        }

        $plug_url = $app; // 返回插件的地址

        $MEMBER = $this->get_cache('member');

        $user = $this->weixin_model->get_user_info($data['openid']);

        if ($user['uid'] && $this->member_model->get_base_member($user['uid'])) {
            // 已绑定
            $member = $this->db->select('password,username,salt,uid')->where('uid', $user['uid'])->get('member')->row_array();
            $this->db->where('id', $user['id'])->update($this->weixin_model->prefix.'_user', array(
                'uid' => $member['uid'],
                'username' => $member['username'],
            ));
            $synlogin = '';
            $code = dr_authcode($member['uid'].'-'.$member['salt'], 'ENCODE');
            $synlogin = '<script type="text/javascript" src="'.SITE_URL.'index.php?c=api&m=synlogin&expire=999999&code='.$code.'"></script>';

            $this->template->assign(array(
                'type' => 0,
                'user' => $user,
                'login' => $synlogin,
                'plug_url' => $plug_url,
                'meta_title' => '授权成功',
            ));
            $this->template->display('weixin.html');
        } else {
            // 未绑定
            $this->template->assign(array(
                'app' => $app,
                'type' => 1,
                'user' => $user,
                'code' => dr_authcode($data['openid'], 'ENCODE'),
                'regfield' => $MEMBER['setting']['regfield'],
                'plug_url' => $plug_url,
                'meta_title' => '账号授权',
            ));
            $this->template->display('weixin.html');
        }
    }

    // 绑定情况判断
    public function ajax_member() {

        $openid = dr_authcode($this->input->post('code'), 'DECODE');
        if (!$openid) {
            exit(dr_json(0, '解码失败，请重试'));
        }

        $user = $this->weixin_model->get_user_info($openid);
        if (!$user) {
            exit(dr_json(0, 'openid获取失败，请重试'));
        } elseif ($user['uid']) {
            exit(dr_json(0, '会员已经注册或者绑定，无需重复操作'));
        }

        // 接收数据
        $data = $this->input->post('data');
        $type = $this->input->post('type');

        if ($type == 1) {
            // 登录
            $rt = $this->member_model->login($data['username'], $data['password'], 36000);
            if (strlen($rt) > 3) {
                // 登录成功
                if (!$this->uid) {
                    exit(dr_json(0, '绑定失败，请重试'));
                }
                // 绑定到此账号
                $user['uid'] = $this->uid;
                $this->db->where('id', $user['id'])->update($this->weixin_model->prefix.'_user', array(
                    'uid' => $user['uid'],
                    'username' => $data['username'],
                ));
                $this->hooks->call_hook('member_login', $data); // 登录成功挂钩点
                exit(dr_json(1, '绑定成功'.$rt));
            } elseif ($rt == -1) {
                $error1 = fc_lang('会员不存在');
            } elseif ($rt == -2) {
                $error1 = fc_lang('密码不正确');
            } elseif ($rt == -3) {
                $error1 = fc_lang('Ucenter注册失败');
            } elseif ($rt == -4) {
                $error1 = fc_lang('Ucenter：会员名称不合法');
            }
            exit(dr_json(0, $error1));
        } else {
            // 注册
            $id = $this->member_model->register($data);
            if ($id > 0) {
                // 注册成功
                $data['uid'] = $this->uid;
                $this->hooks->call_hook('member_register_after', $data); // 注册之后挂钩点
                // 注册后的登录
                $rt = $this->member_model->login($id, $data['password'], 86400, 0, 1);
                // 绑定到此账号
                $user['uid'] = $id;
                $this->db->where('id', $user['id'])->update($this->weixin_model->prefix.'_user', array(
                    'uid' => $user['uid'],
                    'username' => $data['username'],
                ));
                // 更新头像
                $dir = SYS_UPLOAD_PATH.'/member/'.$id.'/';
                !is_dir($dir) && dr_mkdirs($dir);
                $avatar = dr_catcher_data($user['headimgurl']);
                foreach (array(30, 45, 90, 180) as $a) {
                    $file = $dir.$a.'x'.$a.'.jpg';
                    file_put_contents($file, $avatar);
                }
                // 成功提示
                exit(dr_json(1, '注册成功'.$rt));
            } elseif ($id == -1) {
                $error = array('name' => 'username', 'msg' => fc_lang('该会员【%s】已经被注册', $data['username']));
            } elseif ($id == -2) {
                $error = array('name' => 'email', 'msg' => fc_lang('邮箱格式不正确'));
            } elseif ($id == -3) {
                $error = array('name' => 'email', 'msg' => fc_lang('该邮箱【%s】已经被注册', $data['email']));
            } elseif ($id == -4) {
                $error = array('name' => 'username', 'msg' => fc_lang('同一IP在限制时间内注册过多'));
            } elseif ($id == -10) {
                $error = array('name' => 'phone', 'msg' => fc_lang('手机号码必须是11位的整数'));
            } elseif ($id == -11) {
                $error = array('name' => 'phone', 'msg' => fc_lang('该手机号码已经注册'));
            }
            exit(dr_json(0, $error['msg']));
        }
    }

    public function sync() {

        $url = urldecode($this->input->get('url'));
        if ($this->uid) {
            // 定向URL
            redirect($url, 'refresh');
            exit;
        } else {
            // 授权信息
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wx['config']['key'].'&redirect_uri='.urlencode(SITE_URL.'index.php?c=weixin&m=member').'&response_type=code&scope=snsapi_base&state='.urlencode($url).'#wechat_redirect';
            redirect($url, 'refresh');
            exit;
        }


    }


    // 初始化用户情况
    private function _init_user() {

        $user = $this->weixin_model->get_user_info($this->data['FromUserName']);
        if (!$user) {
            // 入库粉丝表
            $user = json_decode(@dr_catcher_data('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.dr_get_access_token().'&openid='.$this->data['FromUserName']), true);
            if (isset($rs['errcode'])) {
                return;
            }
            $user['id'] = $this->weixin_model->add_user($user);
            $user['uid'] = 0; // 加入粉丝表中
        }
        // 当用户没有绑定会员的情况时
        if (!$user['uid']) {
            // 先判断这个微信是否使用过快捷登录
            $oauth = $this->db->where('oauth', 'weixin')->where('oid', $user['openid'])->get('member_oauth')->row_array();
            if (!$oauth) {
                if ($this->wx['config']['user_type']) {
                    // 将微信号自动注册本站会员
                    $uid = $this->member_model->_register(array(
                        'nickname' => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', dr_deal_emoji($user['nickname'])), // 处理昵称字符
                        'avatar' => $user['headimgurl'],
                    ), 'weixin');
                    $this->db->where('id', $user['id'])->update($this->weixin_model->prefix.'_user', array(
                        'uid' => $uid,
                    ));
                } elseif ($this->wx['config']['is_tuser']) {
                    // 提醒用户绑定会员
                    $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wx['config']['key'].'&redirect_uri='.urlencode(SITE_URL.'index.php?c=weixin&m=member').'&response_type=code&scope=snsapi_base&state=member#wechat_redirect';
                    $txt = $this->wx['config']['txt_tuser'] ? $this->wx['config']['txt_tuser'] : '您好，您还没有绑定会员呢，点我赶紧绑定吧';
                    $this->replyText('<a href="'.$url.'">'.$txt.'</a>');
                    return;
                }
            } else {
                $user = $oauth;
            }
        }

        // 存储cookie
        $this->uid = $user['uid'];
        $this->member = $this->member_model->get_member($this->uid);
        $this->db->where('id', $user['id'])->update($this->weixin_model->prefix.'_user', array(
            'uid' => $this->member['uid'],
            'username' => $this->member['username'],
        ));
        $this->input->set_cookie('weixin_uid', $this->member['uid'], 86400);
        $this->input->set_cookie('member_uid', $this->member['uid'], 86400);
        $this->input->set_cookie('member_cookie', substr(md5(SYS_KEY.$this->member['password']), 5, 20), 86400);

        return $this->uid;
    }



    /* ========================发送被动响应消息 begin================================== */
    /* 回复文本消息 */
    private function replyText($content) {
        $msg ['Content'] = $content;
        $this->_replyData ( $msg, 'text' );
    }
    /* 回复图片消息 */
    private function replyImage($media_id) {
        $msg ['Image'] ['MediaId'] = $media_id;
        $this->_replyData ( $msg, 'image' );
    }
    /* 回复语音消息 */
    private function replyVoice($media_id) {
        $msg ['Voice'] ['MediaId'] = $media_id;
        $this->_replyData ( $msg, 'voice' );
    }
    /* 回复视频消息 */
    private function replyVideo($media_id, $title = '', $description = '') {
        $msg ['Video'] ['MediaId'] = $media_id;
        $msg ['Video'] ['Title'] = $title;
        $msg ['Video'] ['Description'] = $description;
        $this->_replyData ( $msg, 'video' );
    }
    /* 回复音乐消息 */
    private function replyMusic($media_id, $title = '', $description = '', $music_url, $HQ_music_url) {
        $msg ['Music'] ['ThumbMediaId'] = $media_id;
        $msg ['Music'] ['Title'] = $title;
        $msg ['Music'] ['Description'] = $description;
        $msg ['Music'] ['MusicURL'] = $music_url;
        $msg ['Music'] ['HQMusicUrl'] = $HQ_music_url;
        $this->_replyData ( $msg, 'music' );
    }
    /*
     * 回复图文消息 articles array 格式如下： array( array('Title'=>'','Description'=>'','PicUrl'=>'','Url'=>''), array('Title'=>'','Description'=>'','PicUrl'=>'','Url'=>'') );
     */
    private function replyNews($articles) {
        $msg ['ArticleCount'] = count ( $articles );
        $msg ['Articles'] = $articles;

        $this->_replyData ( $msg, 'news' );
    }
    /* 发送回复消息到微信平台 */
    private function _replyData($msg, $msgType) {
        $msg ['ToUserName'] = $this->data['FromUserName'];
        $msg ['FromUserName'] = $this->data['ToUserName'];
        $msg ['CreateTime'] = SYS_TIME;
        $msg ['MsgType'] = $msgType;

        $xml = new SimpleXMLElement ( '<xml></xml>' );
        $this->_data2xml ( $xml, $msg );
        $str = $xml->asXML ();

        echo ($str);exit;
    }
    /* 组装xml数据 */
    public function _data2xml($xml, $data, $item = 'item') {
        foreach ( $data as $key => $value ) {
            is_numeric ( $key ) && ($key = $item);
            if (is_array ( $value ) || is_object ( $value )) {
                $child = $xml->addChild ( $key );
                $this->_data2xml ( $child, $value, $item );
            } else {
                if (is_numeric ( $value )) {
                    $child = $xml->addChild ( $key, $value );
                } else {
                    $child = $xml->addChild ( $key );
                    $node = dom_import_simplexml ( $child );
                    $node->appendChild ( $node->ownerDocument->createCDATASection ( $value ) );
                }
            }
        }
    }
    /* ========================发送被动响应消息 end================================== */

}

