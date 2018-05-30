<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Site extends M_Controller {
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('网站管理') => array('admin/site/index', 'globe'),
				fc_lang('添加') => array('admin/site/add_js', 'plus'),
			))
		));
		
		$this->load->model('site_model');
		$this->load->library('dconfig');
    }
	
	/**
     * 切换
     */
    public function select() {
	
		$id	= (int)$this->input->post('id');
		if (!isset($this->site_info[$id])) {
            exit(dr_json(0, fc_lang('域名配置文件中站点(#%s)不存在', $id)));
        }

		$this->session->set_userdata('siteid', $id); // 保存Session
		exit(dr_json(1, fc_lang('成功切换到站点[%s]，正在刷新页面...', $this->site_info[$id]['SITE_NAME']), $id));
	}

    /**
     * 管理
     */
    public function index() {
	
		if (IS_POST) {
			$ids = $this->input->post('ids');
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
			$_data = $this->input->post('data', true);
			foreach ($ids as $id) {
                if ($this->db->where('id<>', (int)$id)->where('domain', $_data[$id]['domain'])->count_all_results('site')) {
                    exit(dr_json(0, fc_lang('域名【%s】已经被使用了', $_data[$id]['domain'])));
                }
				$this->db->where('id', (int)$id)->update('site', $_data[$id]);
			}
            $this->site_model->cache();
            $this->system_log('修改网站站点【#'.@implode(',', $ids).'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功')));
		}

		$this->template->assign('list', $this->site_model->get_site_data());
		$this->template->display('site_index.html');
	}
	
	/**
     * 添加
     */
    public function add() {
	
		if (!IS_POST) {

			$this->load->library('dconfig');
			$data = $this->input->post('data', TRUE);
			$domain	= require WEBPATH.'config/domain.php';

            if (!$data['name']) {
                exit(dr_json(0, fc_lang('网站名字不存在'), 'name'));
            } elseif (!preg_match('/[\w-_\.]+\.[\w-_\.]+/i', $data['domain'])) {
                exit(dr_json(0, fc_lang('域名不规范'), 'domain'));
            } elseif (in_array($data['domain'], $domain)) {
                exit(dr_json(0, fc_lang('%s已经存在', $data['domain']), 'domain'));
            } elseif ($this->db->where('domain', $data['domain'])->count_all_results('site')) {
                exit(dr_json(0, fc_lang('域名【%s】已经被使用了', $data['domain']), 'domain'));
            }
			// 初始化网站配置
			$cfg['SITE_NAME'] = $data['name'];
			$cfg['SITE_DOMAIN'] = $data['domain'];
			$cfg['SITE_DOMAINS'] = '';
			$cfg['SITE_TIMEZONE'] = '8';
			$cfg['SITE_LANGUAGE'] = 'zh-cn';
			$cfg['SITE_TIME_FORMAT'] = 'Y-m-d H:i';
			// 入库
			$data['setting'] = $cfg;
			$id	= $this->site_model->add_site($data);
			if (!$id) {
                exit(dr_json(0, dr_fc_lang('数据异常，入库失败')));
            }

			// 保存域名
			$domain[$data['domain']] = $id;
			$size = $this->dconfig->file(WEBPATH.'config/site/'.$id.'.php')->note('站点配置文件')->space(32)->to_require_one($this->site_model->config, $cfg);
			if (!$size) {
                exit(dr_json(0, fc_lang('网站域名文件创建失败，请检查config目录权限')));
            }
			$size = $this->dconfig->file(WEBPATH.'config/domain.php')->note('站点域名文件')->space(32)->to_require_one($domain);
			if (!$size) {
                exit(dr_json(0, fc_lang('站点配置文件创建失败，请检查config目录权限')));
            }
            $this->site_model->cache();
            $this->system_log('添加网站站点【#'.$id.'】'.$data['name']); // 记录日志
			exit(dr_json(1, fc_lang('操作成功')));
		} else {
			$this->template->display('site_add.html');
		}
    }
	
	/**
     * 站点配置
     */
    public function config() {

		$id = isset($_GET['id']) ? max((int)$_GET['id'], 1) : SITE_ID;
		$data = $this->site_model->get_site_info($id);
		if (!$data) {
            $this->admin_msg(fc_lang('域名配置文件中站点(#%s)不存在', $id));
        }

        $result	= '';

		if (IS_POST) {
			$cfg = $this->input->post('data', true);
			$cfg['SITE_DOMAIN'] = $this->input->post('domain', true);
            // 查询非当前站点绑定的域名
            $as = array();
            $all = $this->db->where('id<>', $id)->get('site')->result_array();
            if ($all) {
                foreach ($all as $b) {
                    $set = dr_string2array($b['setting']);
                    $as[] = $b['domain'];
                    if ($set['SITE_MOBILE']) {
                        $as[] = $set['SITE_MOBILE'];
                    }
                    if ($set['SITE_DOMAINS']) {
                        $_arr = @explode(',', $set['SITE_DOMAINS']);
                        if ($_arr) {
                            foreach ($_arr as $_a) {
                                if ($_a) {
                                    $as[] = $_a;
                                }
                            }
                        }
                    }
                }
            }
            // 判断域名是否可用
            if (in_array($cfg['SITE_DOMAIN'], $as)) {
                $result = fc_lang('域名【%s】已经被使用了', $cfg['SITE_DOMAIN']);
            } else {
                $cfg['SITE_DOMAINS'] = str_replace(PHP_EOL, ',', $cfg['SITE_DOMAINS']);
                // 多域名验证
                if ($cfg['SITE_DOMAINS']) {
                    $arr = @explode(',', $cfg['SITE_DOMAINS']);
                    if ($arr) {
                        foreach ($arr as $a) {
                            if (in_array($a, $as)
                                || $a == $cfg['SITE_DOMAIN']
                                || $a == $cfg['SITE_MOBILE']) {
                                $result = fc_lang('域名【%s】已经被使用了', $a);
                                break;
                            }
                        }
                    }
                }
                if (!$result) {
                    $cfg['SITE_IMAGE_CONTENT'] = $cfg['SITE_IMAGE_WATERMARK'] ? $cfg['SITE_IMAGE_CONTENT'] : 0;
                    $data = array(
                        'name' => $cfg['SITE_NAME'],
                        'domain' => $cfg['SITE_DOMAIN'],
                        'setting' => $cfg
                    );
                    $this->site_model->edit_site($id, $data);
                    $domain	= require WEBPATH.'config/domain.php';
                    $domain[$cfg['SITE_DOMAIN']] = $id;
                    if ($cfg['SITE_MOBILE']) {
                        $domain[$cfg['SITE_MOBILE']] = $id;
                    }
                    $this->dconfig->file(WEBPATH.'config/site/'.$id.'.php')->note('站点配置文件')->space(32)->to_require_one($this->site_model->config, $cfg);
                    $this->dconfig->file(WEBPATH.'config/domain.php')->note('站点域名文件')->space(32)->to_require_one($domain);
                    $result	= 1;
                }
            }
            $data = $cfg;
            $this->site_model->cache();
			// 删除站点首页缓存
			$this->load->helper('file');
			delete_files(WEBPATH.'cache/index/');
            $this->system_log('配置网站站点【#'.$id.'】'.$data['name']); // 记录日志
		}
		
		$this->load->helper('directory');
		$files = directory_map(WEBPATH.'statics/watermark/', 1);
		$opacity = array();
		foreach ($files as $t) {
			if (substr($t, -3) == 'ttf') {
				$font[] = $t;
			} else {
				$opacity[] = $t;
			}
		}

		$template_path = dr_dir_map(TPLPATH.'pc/', 1);


		$this->template->assign(array(
			'id' => $id,
            'ip' => $this->_get_server_ip(),
            'data' => $data,
			'page' => max((int)$this->input->post('page'), 0),
			'lang' => dr_dir_map(FCPATH.'dayrui/language/', 1),
			'theme' => dr_get_theme(),
			'result' => $result,
			'is_theme' => strpos($data['SITE_THEME'], 'http://') === 0 ? 1 : 0,
			'wm_opacity' => $opacity,
			'wm_font_path' => $font,
			'template_path' => $template_path,
			'wm_vrt_alignment' => array('top', 'middle', 'bottom'),
			'wm_hor_alignment' => array('left', 'center', 'right'),
		));
		$this->template->display('site_config.html');
    }
	
	/**
     * 删除
     */
    public function del() {
		$id = (int)$this->input->get('id');
		if (!$this->site_info[$id]) {
            $this->admin_msg(fc_lang('站点不存在，请尝试更新一次缓存'));
        }
		if ($id == 1) {
            $this->admin_msg(fc_lang('主站点不能删除'));
        }
		// 卸载模型
		$module = $this->db->get('module')->result_array();
		if ($module) {
			$this->load->model('module_model');
			foreach ($module as $t) {
				$this->module_model->uninstall($t['dirname'], $id);
			}
		}
		// 删除表
        $this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix($id.'_form').'`');
        $this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix($id.'_block').'`');
        $this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix($id.'_tag').'`');
        $this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix($id.'_index').'`');
        $this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix($id.'_category').'`');
		// 删除站点
		$this->db->delete('site', 'id='.$id);
        // 删除字段
		$this->db->where('relatedid', $id)->where('relatedname', 'category')->delete('field');
		// 删除该站配置
		unlink(WEBPATH.'config/site/'.$id.'.php');
		// 删除该站附件
		$this->load->model('attachment_model');
		$this->attachment_model->delete_for_site($id);
        $this->system_log('删除网站站点【#'.$id.'】'); // 记录日志
		$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('site/index'), 1);
    }
	
	/**
     * 缓存
     */
    public function cache() {
		$this->site_model->cache();
        (int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}