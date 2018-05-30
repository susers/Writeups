<?php

class Member_model extends M_Model {

    /**
     * 会员修改信息
     */
    public function edit($main, $data) {

        if (isset($main['check']) && $main['check']) {
            $main['ismobile'] = 1;
            $main['randcode'] = '';
            unset($main['check'], $main['phone']);
        }

        if (isset($main['check'])) {
            unset($main['check']);
        }

        $this->db->where('uid', $this->uid)->update('member', $main);

        $data['uid'] = $this->uid;
        $data['complete'] = 1;

        $this->db->replace('member_data', $data);

        return TRUE;
    }

    /**
     * 会员基本信息
     */
    public function get_base_member($key, $type = 0) {

        if (!$key) {
            return NULL;
        }

        $type ? $this->db->where('username', $key) : $this->db->where('uid', (int)$key);

        $data = $this->db
                     ->limit(1)
                     ->select('uid,username,email,levelid,groupid,score,experience')
                     ->get('member')
                     ->row_array();
        if (!$data) {
            return NULL;
        }

        return $data;
    }

    public function get_markrule($uid) {
        return 0;
    }


    /**
     * 会员信息
     */
    public function get_member($uid) {

        $uid = intval($uid);
        if (!$uid) {
            return NULL;
        }

        // 查询会员信息
        $db = $this->db
                     ->from($this->db->dbprefix('member').' AS m2')
                     ->join($this->db->dbprefix('member_data').' AS a', 'a.uid=m2.uid', 'left')
                     ->where('m2.uid', $uid)
                     ->limit(1)
                     ->get();
        if (!$db) {
            return NULL;
        }
        $data  = $db->row_array();
        if (!$data) {
            return NULL;
        }

        $data['uid'] = $uid;
        $data['avatar_url'] = '';
        if (defined('UCSSO_API')) {
            $data['avatar_url'] =  ucsso_get_avatar($uid);
        } else {
            foreach (array('png', 'jpg', 'gif', 'jpeg') as $ext) {
                if (is_file(SYS_UPLOAD_PATH.'/member/'.$uid.'/45x45.'.$ext)) {
                    $data['avatar_url'] = SYS_ATTACHMENT_URL.'member/'.$uid.'/45x45.'.$ext;
                    break;
                }
            }
            $data['avatar_url'] = $data['avatar_url'] ? $data['avatar_url'] : THEME_PATH.'admin/images/avatar_45.png';
        }



        return $data;
    }

    /**
     * 通过会员id取会员名称
     */
    function get_username($uid) {

        if (!$uid) {
            return NULL;
        }

        $data = $this->db->select('username')->where('uid', (int)$uid)->limit(1)->get('member')->row_array();

        return $data['username'];
    }

    public function upgrade($uid, $groupid, $limit, $time = 0) {

    }

    /**
     * 后台管理员验证登录
     */
    public function admin_login($username, $password) {

        $password = trim($password);
        // 查询用户信息
        $data = $this->db
                     ->select('`password`, `salt`, `adminid`,`uid`')
                     ->where('username', $username)
                     ->limit(1)
                     ->get('member')
                     ->row_array();
        // 判断用户状态
        if (!$data) {
            return -1;
        } elseif (md5(md5($password).$data['salt'].md5($password)) != $data['password']) {
            return -2;
        } elseif ($data['adminid'] == 0) {
            return -3;
        }

        // 保存会话
        $this->session->set_userdata('uid', $data['uid']);
        $this->session->set_userdata('admin', $data['uid']);
        $this->input->set_cookie('member_uid', $data['uid'], 86400);
        $this->input->set_cookie('member_cookie', substr(md5(SYS_KEY . $data['password']), 5, 20), 86400);

        return $data['uid'];
    }

    /**
     * 管理员用户信息
     */
    public function get_admin_member($uid, $verify = 0) {

        // 查询用户信息
        $data = $this->db
                     ->select('m.uid,m.email,m.username,m.adminid,m.groupid,a.realname,a.usermenu,a.color')
                     ->from($this->db->dbprefix('member').' AS m')
                     ->join($this->db->dbprefix('admin').' AS a', 'a.uid=m.uid', 'left')
                     ->where('m.uid', $uid)
                     ->limit(1)
                     ->get()
                     ->row_array();
        if (!$data) {
            return 0;
        } elseif ($verify) {
            // 判断用户状态
            if ($data['adminid'] == 0) {
                return -3;
            }
        }

        $role = $this->dcache->get('role');
        $data['role'] = $role[$data['adminid']];
        $data['usermenu'] = dr_string2array($data['usermenu']);

        return $data;
    }



    /**
     * 管理人员
     */
    public function get_admin_all($roleid = 0, $keyword = NULL) {

        $select = $this->db
                       ->from($this->db->dbprefix('admin').' AS a')
                       ->join($this->db->dbprefix('member').' AS b', 'a.uid=b.uid', 'left');
        $keyword && $select->like('b.username', $keyword);

        return $select->get()->result_array();
    }

    /**
     * 添加管理人员
     */
    public function insert_admin($insert, $update, $uid) {
        $this->db->where('uid', $uid)->update('member', $update);
        $this->db->replace('admin', $insert);
    }

    /**
     * 修改管理人员
     */
    public function update_admin($insert, $update, $uid) {
        $this->db->where('uid', $uid)->update('member', $update);
        $this->db->where('uid', $uid)->update('admin', $insert);
    }

    /**
     * 移除管理人员
     */
    public function del_admin($uid) {

        if ($uid == 1) {
            return NULL;
        }

        $this->db->where('uid', $uid)->delete('admin');
        $this->db->where('uid', $uid)->update('member', array('adminid' => 0));
    }

    /**
     * 前端会员验证登录
     */
    public function login($username, $password, $expire, $back = 0, $is_uid = 0) {

        // 查询会员信息
        if ($is_uid) {
            $data = $this->db->where('uid', (int)$username)->get('member')->row_array();
            $username = $data['username'];
        } else {
            $data = $this->db->where('username', $username)->get('member')->row_array();
        }

        $MEMBER = $this->ci->get_cache('member');
        $synlogin = '';

        // 同步登录
        if (defined('UCSSO_API')) {
            /*
                    1:表示用户登录成功
                   -1:用户名不合法
                   -2:密码不合法
                   -3:用户名不存在
                   -4:密码不正确
               */
            $rt = ucsso_login($username, $password);
            if ($rt['code'] < 0) {
                if ($rt['code'] == -3) {
                    // 当ucsso用户不存在时，在验证本地库
                    !$data && $data = dr_vip_login($this->db, $username);
                    if ($data) {
                        //如果本地库有，我们就同步到服务器去
                        $rt = ucsso_register($username, $password, $data['email'], $data['phone']);
                        if (!$rt) {
                            return -404; # 网络异常
                        }
                        //var_dump($rt);exit;
                        if ($rt['code']) {
                            // 注册成功了
                            // 上报uid
                            $rt2 = ucsso_syncuid($rt['code'], $data['uid']);
                            if (!$rt2['code']) {
                                return -5; #同步uid失败
                            }
                            $synlogin.= ucsso_synlogin($data['uid']);
                        } else {
                            return 0;
                        }
                    }
                } elseif ($rt['code'] == -1) {
                    return -1;
                } elseif ($rt['code'] == -2) {
                    return -2;
                } elseif ($rt['code'] == -3) {
                    return -1;
                } elseif ($rt['code'] == -4) {
                    return -2;
                } elseif ($rt['code'] == -404) {
                    return -404;
                }
            } elseif (!$rt['data']['uid']) {
                // 表示ucsso存在这个账号，但没有注册uid
                $ucsso_id = $rt['data']['ucsso_id'];
                if (!$data) {
                    // 本地有会员不存在时就重新注册
                    $data['uid'] = $this->_register(array(
                        'username' => $username,
                        'password' => $password,
                        'email' => $rt['data']['email'],
                        'phone' => $rt['data']['phone'],
                    ));
                    if (!$data['uid']) {
                        return -3;
                    }
                }
                // 上报uid
                $rt = ucsso_syncuid($ucsso_id, $data['uid']);
                if (!$rt['code']) {
                    return -55;
                }
            }
            $synlogin.= ucsso_synlogin($data['uid']);
        } else {
            // 会员不存在
            if (!$data) {
                return -1;
            }
            // 密码验证
            $password = trim($password);
            if (md5(md5($password).$data['salt'].md5($password)) != $data['password']) {
                return -2;
            }
        }

		$this->ci->uid = $data['uid'];



        $expire = $expire ? $expire : 36000;
        $MEMBER['synurl'][] = '/';
        foreach ($MEMBER['synurl'] as $url) {
            $code = dr_authcode($data['uid'].'-'.$data['salt'], 'ENCODE');
            $synlogin.= '<script type="text/javascript" src="'.$url.'/index.php?c=api&m=synlogin&expire='.$expire.'&code='.$code.'"></script>';
        }


        $this->input->set_cookie('member_uid', $data['uid'], 86400);
        $this->input->set_cookie('member_cookie', substr(md5(SYS_KEY . $data['password']), 5, 20), 86400);

        return $synlogin;
    }


    /**
     * 前端会员退出登录
     */
    public function logout() {

        // 注销授权登陆的会员
        if ($this->session->userdata('member_auth_uid')) {
            $this->session->set_userdata('member_auth_uid', 0);
            return;
        }

        $synlogin = '';
        $MEMBER = $this->ci->get_cache('member');
        $MEMBER['setting']['ucenter'] && $synlogin.= uc_user_synlogout();
        defined('UCSSO_API') && $synlogin.= ucsso_synlogout();

        foreach ($MEMBER['synurl'] as $url) {
            $synlogin.= '<script type="text/javascript" src="'.$url.'/index.php?c=api&m=synlogout"></script>';
        }
        $synlogin.= '<script type="text/javascript" src="/index.php?c=api&m=synlogout"></script>';

        return $synlogin;
    }

    /**
     * 注册会员 验证
     */
    public function register($data, $groupid = NULL, $uid = NULL) {

        $setting = $this->ci->get_cache('member', 'setting');
        $this->ucsynlogin = $this->synlogin = '';



        !$data['username'] && $data['phone'] && $data['username'] = $data['phone'];
        !$data['username'] && $data['email'] && $data['username'] = $data['email'];

        // 验证邮箱
        if (!$data['email'] || !preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $data['email'])) {
            return -2;
        } elseif ($this->db->where('email', $data['email'])->count_all_results('member')) {
            return -3;
        }

        /*
        // 验证手机
        if (@in_array('phone', $setting['regfield'])) {
            if (strlen($data['phone']) != 11 || !is_numeric($data['phone'])) {
                return -10;
            } elseif ($this->db->where('phone', $data['phone'])->count_all_results('member')) {
                return -11;
            }
        }*/

        // 验证账号
        if ($this->db->where('username', $data['username'])->count_all_results('member')) {
            return -1;
        }

        // UCSSO_API 注册判断
        if (defined('UCSSO_API')) {
            /*
                    大于 0:返回用户 ID，表示用户注册成功
                     0:失败
                    -1:用户名不合法
                    -2:用户名已经存在
                    -3:Email 格式有误
                    -4:该 Email 已经被注册
                    -5:该 手机号码 格式有误
                    -6:该 手机号码 已经被注册
                */
            $rt = ucsso_register($data['username'], $data['password'], $data['email'], $data['phone']);
            if ($rt['code'] == -1) {
                return -5;
            } elseif ($rt['code'] == -2) {
                return -1;
            } elseif ($rt['code'] == -3) {
                return -2;
            } elseif ($rt['code'] == -4) {
                return -3;
            } elseif ($rt['code'] == -5) {
                return -10;
            } elseif ($rt['code'] == -6) {
                return -11;
            } elseif ($rt['code'] == 0) {
                return 0;
            }
            $this->ucsso_id = (int)$rt['code'];
        }

        return $this->_register($data, NULL, $groupid, $uid);
    }

    /**
     * 注册会员 入库
     */
    public function _register($data, $OAuth = NULL, $groupid = NULL, $uid = NULL) {

        $salt = substr(md5(rand(0, 999)), 0, 10); // 随机10位密码加密码
        $regverify = $this->ci->get_cache('member', 'setting', 'regverify');

        if ($uid) {
            // OAuth组转换为普通组
            $data['email'] = strtolower($data['email']);
            $data['phone'] = trim($data['phone']);
            $data['password'] = trim($data['password']);
            $groupid = 3;
            $this->db->where('uid', (int) $uid)->update('member', array(
                'salt' => $salt,
                'email' => $data['email'],
                'groupid' => $groupid,
                'username' => $data['username'],
                'password' => md5(md5($data['password']).$salt.md5($data['password']))
            ));
        } else {
            // 正常注册时，会员初始化信息
            $data['email'] = strtolower($data['email']);
            $data['password'] = trim($data['password']);
            $groupid = $groupid ? $groupid : ($regverify ? 1 : 3);
            $randcode = $regverify == 3 ? rand(100000, 999999) : 0;
            $this->db->insert('member', array(
                'salt' => $salt,
                'name' => '',
                'phone' => $data['phone'] ? $data['phone'] : '',
                'regip' => $this->input->ip_address(),
                'email' => $data['email'],
                'money' => 0,
                'score' => 0,
                'spend' => 0,
                'avatar' => '',
                'freeze' => 0,
                'regtime' => SYS_TIME,
                'groupid' => $groupid,
                'levelid' => 0,
                'overdue' => 0,
                'username' => $data['username'],
                'password' => md5(md5($data['password']).$salt.md5($data['password'])),
                'randcode' => $randcode,
                'ismobile' => 0,
                'experience' => 0,
            ));
            $uid = $this->db->insert_id();
            if ($regverify == 1) {
                // 邮件审核
                $url = dr_member_url('login/verify').'&code='.$this->get_encode($uid);
                $this->sendmail($data['email'], fc_lang('会员注册-邮件验证'), fc_lang(@file_get_contents(WEBPATH.'cache/email/verify.html'), $data['username'], $url, $url, $this->input->ip_address()));
            } elseif ($regverify == 3) {
                // 手机审核
                $this->sendsms($data['phone'], fc_lang('尊敬的用户，您的本次验证码是：%s', $randcode));
            } elseif ($regverify == 2) {
                // 人工审核
                $this->admin_notice('member', fc_lang('新会员【%s】注册审核', $data['username']), 'member/admin/home/index/field/uid/keyword/'.$uid);
            }
        }


        // uid 同步
        if ($this->ucsso_id && defined('UCSSO_API')) {
            $rt = ucsso_syncuid($this->ucsso_id, $uid);
            if (!$rt['code']) {
                // 同步失败
                log_message('error', 'UCSSO同步uid失败：'.$rt['msg']);
            }
        }



        return $uid;
    }

    // 修改邮箱和密码
    public function edit_email_password($username, $data) {

        // 验证本站会员
        if (!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $data['email'])) {
            return -2;
        } elseif ($this->db->where('email', $data['email'])->count_all_results('member')) {
            return -3;
        }
        // 验证UCenter
        if (defined('UC_KEY')) {
            $ucid = uc_user_edit($username, NULL, $data['password'], $data['email'], 1);
            if ($ucid == -1) {
                return -5;
            } elseif ($ucid == -2) {
                return -6;
            } elseif ($ucid == -4) {
                return -7;
            } elseif ($ucid == -5) {
                return -8;
            } elseif ($ucid == -6) {
                return -9;
            }
        }
        // 修改资料
        $salt = substr(md5(rand(0, 999)), 0, 10); // 随机10位密码加密码
        $data['password'] = trim($data['password']);
        $this->db->where('username', $username)->update('member', array(
            'salt' => $salt,
            'email' => $data['email'],
            'groupid' => 3,
            'password' => md5(md5($data['password']).$salt.md5($data['password']))
        ));
    }

    /**
     * 取会员COOKIE
     */
    public function member_uid($login = 0) {

        if (!$login && IS_MEMBER && $uid = $this->session->userdata('member_auth_uid')) {
            return $uid;
        } else {
            $uid = (int)get_cookie('member_uid');
            if (!$uid) {
                return NULL;
            }
            if (!$this->session->userdata('uid')) {
                $this->session->set_userdata('uid', $uid); // 更新会员活动时间
            }
            return $uid;
        }
    }

    // 验证会员有效性
    public function check_member_login() {

        // 授权登陆时不验证
        if ($this->uid && $this->session->userdata('member_auth_uid') == $this->uid) {
            return 1;
        }

        $cookie = get_cookie('member_cookie');
        if (!$cookie) {
            return 0;
        }

        if (substr(md5(SYS_KEY.$this->member['password']), 5, 20) !== $cookie) {
            if (defined('UCSSO_API')) {
                $rt = ucsso_get_password($this->uid);
                if ($rt['code']) {
                    // 变更本地库
                    $this->db->where('uid', $this->uid)->update('member', array(
                        'salt' => $rt['data']['salt'],
                        'password' => $rt['data']['password'],
                    ));
                }
            }
            return 0;
        }

        return 1;
    }

    /**
     * 会员配置信息
     */
    public function setting() {

        $data = array();
        if (is_file(WEBPATH.'config/member.php')) {
            $data = file_get_contents(WEBPATH.'config/member.php');
            $data = dr_string2array(substr($data, 13));
        }

        return $data;
    }

    /**
     * 会员配置
     */
    public function member($set) {

        $size = file_put_contents(WEBPATH.'config/member.php', '<?php exit;?>'.dr_array2string($set));
        if (!$size) {
            $this->ci->admin_msg('文件config/member.php无法写入');
        }
        return $set;
    }



    /**
     * 会员缓存
     */
    public function cache() {

        $cache = array();
        $this->dcache->delete('member');

        // 会员自定义字段
        $field = $this->db
                      ->where('disabled', 0)
                      ->where('relatedid', 0)
                      ->where('relatedname', 'member')
                      ->order_by('displayorder ASC,id ASC')
                      ->get('field')
                      ->result_array();
        if ($field) {
            foreach ($field as $t) {
                $t['setting'] = dr_string2array($t['setting']);
                $cache['field'][$t['fieldname']] = $t;
            }
        }

        $cache['setting'] = $this->setting();

        $domain = require WEBPATH.'config/domain.php'; // 加载站点域名配置文件
        $cache['synurl'] = array();
        // 增加到登录同步列表中
        foreach ($this->site_info as $sid => $t) {
            // 主站点域名
            $cache['synurl'][] = dr_http_prefix($t['SITE_DOMAIN']);
            // 移动端域名
            $t['SITE_MOBILE'] && $cache['synurl'][] = dr_http_prefix($t['SITE_MOBILE']);
            // 将站点的域名配置文件加入同步列表中
            foreach ($domain as $url => $site_id) {
                if ($url && $site_id == $sid) {
                    if ($t['SITE_DOMAIN'] != $url && $t['SITE_MOBILE'] != $url) {
                        // 筛选出站点域名和移动端域名
                        $cache['synurl'][] = dr_http_prefix($url);
                    }
                }
            }
        }
        $cache['synurl'] = array_unique($cache['synurl']);



        // 更新UCSSO配置
        if ($cache['setting']['ucsso']) {
            $ucsso = htmlspecialchars_decode($cache['setting']['ucssocfg']);
            if (strpos($ucsso, 'eval') !== false
                || strpos($ucsso, '_POST') !== false
                || strpos($ucsso, '_REQUEST') !== false
                || strpos($ucsso, '_GET') !== false) {
                return;
            }
            file_put_contents(WEBPATH.'api/ucsso/config.php', $ucsso , LOCK_EX);
        }


        $this->ci->clear_cache('member');
        $this->dcache->set('member', $cache);

        return $cache;
    }

    /**
     * 条件查询
     */
    private function _where(&$select, $data) {


        // 存在POST提交时，重新生成缓存文件
        if (IS_POST) {
            $data = $this->input->post('data');
            foreach ($data as $i => $t) {
                if ($t == '') {
                    unset($data[$i]);
                }
            }
        }

        // 存在search参数时，读取缓存文件
        if ($data) {
            if (isset($data['keyword']) && $data['keyword'] != '' && $data['field']) {
                if ($data['field'] == 'uid') {
                    // 按id查询
                    $id = array();
                    $ids = explode(',', $data['keyword']);
                    foreach ($ids as $i) {
                        $id[] = (int)$i;
                    }
                    $select->where_in('uid', $id);
                } elseif ($data['field'] == 'ismobile') {
                    $select->where($data['field'], intval($data['keyword']));
                } elseif (in_array($data['field'], array('complete', 'is_auth'))) {
                    $select->where('uid IN (select uid from `'.$this->db->dbprefix('member_data').'` where `'.$data['field'].'` = '.intval($data['keyword']).')');
                } elseif (in_array($data['field'], array('phone', 'name', 'email', 'username'))) {
                    $select->like($data['field'], urldecode($data['keyword']));
                } else {
                    // 查询附表字段
                    $select->where('uid IN (select uid from `'.$this->db->dbprefix('member_data').'` where `'.$data['field'].'` LIKE "%'.urldecode($data['keyword']).'%")');
                }
            }
        }


        return $data;
    }

    /**
     * 数据分页显示
     */
    public function limit_page($param, $page, $total) {

        if (!$total || IS_POST) {
            $select = $this->db->select('count(*) as total');
            $_param = $this->_where($select, $param);
            $data = $select->get('member')->row_array();
            unset($select);
            $total = (int) $data['total'];
            if (!$total) {
                $_param['total'] = 0;
                return array(array(), $_param);
            }
            $page = 1;
        }

        $select = $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $_param = $this->_where($select, $param);
        $order = dr_get_order_string(isset($_GET['order']) && strpos($_GET['order'], "undefined") !== 0 ? $this->input->get('order', TRUE) : 'uid desc', 'uid desc');
        $data = $select->order_by($order)->get('member')->result_array();
        $_param['total'] = $total;
        $_param['order'] = $order;

        return array($data, $_param);
    }

    /**
     * 更新分数
     */
    public function update_score($type, $uid, $val, $mark, $note = '', $count = 0) {

        if (!$uid || !$val) {
            return NULL;
        }

        $table = $this->db->dbprefix('member_scorelog');
        if ($count && $this->db->where('type', (int)$type)->where('mark', $mark)->count_all_results($table) >= $count) {
            return NULL;
        }

        $data = $this->db->select('score,experience')->where('uid', $uid)->get('member')->row_array();
        $score = $type ? (int)$data['score'] : (int)$data['experience'];
        $value = $score + $val;
        $value = $value > 0 ? $value : 0; // 不允许积分或虚拟币小于0
        unset($data);

        // 更新
        $type ? $this->db->where('uid', (int)$uid)->update('member', array('score' => $value)) : $this->db->where('uid', (int)$uid)->update('member', array('experience' => $value));

        unset($value);

        $this->db->insert($table, array(
            'uid' => $uid,
            'type' => $type,
            'mark' => $mark,
            'note' => $note,
            'value' => $val,
            'inputtime' => SYS_TIME,
        ));

        return $this->db->insert_id();
    }

    /**
     * 会员初始化处理
     */
    public function init_member() {

    }

    /**
     * 邮件发送
     */
    public function sendmail($tomail, $subject, $message) {

        if (!$tomail || !$subject || !$message) {
            return FALSE;
        }

        $cache = $this->ci->get_cache('email');
        if (!$cache) {
            return NULL;
        }

        $this->load->library('Dmail');
        foreach ($cache as $data) {
            $this->dmail->set(array(
                'host' => $data['host'],
                'user' => $data['user'],
                'pass' => $data['pass'],
                'port' => $data['port'],
                'from' => $data['user'],
            ));
            if ($this->dmail->send($tomail, $subject, $message)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * 短信发送
     */
    public function sendsms($mobile, $content) {

        if (!$mobile || !$content) {
            return FALSE;
        }

        $file = WEBPATH.'config/sms.php';
        if (!is_file($file)) {
            return FALSE;
        }

        $config = require_once $file;
        if ($config['third']) {
            $this->load->helper('sms');
            if (function_exists('my_sms_send')) {
                $result = my_sms_send($mobile, $content, $config);
            } else {
                return FALSE;
            }
        } else {
            $result = dr_catcher_data('http://sms.dayrui.com/index.php?uid='.$config['uid'].'&key='.$config['key'].'&mobile='.$mobile.'&content='.$content.'【'.$config['note'].'】&domain='.trim(str_replace('http://', '', SITE_URL), '/').'&sitename='.SITE_NAME);
            if (!$result) {
                return FALSE;
            }
            $result = dr_object2array(json_decode($result));
        }

        @file_put_contents(WEBPATH.'cache/sms_error.log', date('Y-m-d H:i:s').' ['.$mobile.'] ['.$result['msg'].'] （'.str_replace(array(chr(13), chr(10)), '', $content).'）'.PHP_EOL, FILE_APPEND);

        return $result;
    }

    /**
     * 验证码加密
     */
    public function get_encode($uid) {
        $randcode = rand(1000, 999999);
        $this->encrypt->set_cipher(MCRYPT_BLOWFISH);
        $this->db->where('uid', $uid)->update('member', array('randcode' => $randcode));
        return $this->encrypt->encode(SYS_TIME.','.$uid.','.$randcode);
    }

    /**
     * 验证码解码
     */
    public function get_decode($code) {
        $code = str_replace(' ', '+', $code);
        $this->encrypt->set_cipher(MCRYPT_BLOWFISH);
        return $this->encrypt->decode($code);
    }

    /**
     * 会员删除
     */
    public function delete($uids) {

        if (!$uids || !is_array($uids)) {
            return NULL;
        }

        $this->load->model('attachment_model');

        foreach ($uids as $uid) {
            if ($uid == 1) {
                continue;
            }
            $tableid = (int)substr((string)$uid, -1, 1);
            // 删除会员表
            $this->db->where('uid', $uid)->delete('member');
            // 删除会员附表
            $this->db->where('uid', $uid)->delete('member_data');
            // 删除管理员表
            $this->db->where('uid', $uid)->delete('admin');
            // 删除附件
            $this->attachment_model->delete_for_uid($uid);
            // 删除会员附件
            $this->load->helper('file');
            delete_files(SYS_UPLOAD_PATH.'/member/'.$uid.'/');
        }
    }

    public function add_notice($uid, $type, $note) {

    }


}
