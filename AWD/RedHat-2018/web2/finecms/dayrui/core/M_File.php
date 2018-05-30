<?php

class M_File extends M_Controller {
	
	public $_dir;
	public $path;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->_dir = array('ckplayer', 'comment', 'watermark', 'avatar', 'js', 'oauth', 'admin', 'space');

    }

    // 初始化
    protected function _init() {
        if (APP_DIR && APP_DIR != 'member' && APP_DIR != 'space') {
			
            $this->path = $this->router->method == 'mobile' || $this->input->get('ismb') ? (is_dir(FCPATH.'module/'.APP_DIR.'/mobiles/') ? FCPATH.'module/'.APP_DIR.'/mobiles/' : TPLPATH.'mobile/web/') : (is_dir(FCPATH.'module/'.APP_DIR.'/templates/') ? FCPATH.'module/'.APP_DIR.'/templates/' : TPLPATH.'pc/web/');
			
			$this->router->class == 'theme' && $this->path = WEBPATH.'statics/';
            $this->template->assign(array(
                'path' => $this->path,
                'furi' => APP_DIR.'/tpl/',
                'auth' => APP_DIR.'/admin/tpl/',
                'menu' => $this->get_menu(array(
                        fc_lang('模板管理') => APP_DIR.'/admin/tpl/index',
                        fc_lang('移动端模板') => APP_DIR.'/admin/tpl/mobile',
                        fc_lang('标签向导') => APP_DIR.'/admin/tpl/tag',
                    )),
                'ismb' => $this->router->method == 'mobile' ? 1 : 0,
            ));
        }
    }

	/**
     * 文件管理
     */
	public function index() {

        $this->_init();

		$this->load->helper('directory');
		$dir = trim(str_replace('.', '', $this->input->get('dir',true)), '/');
		$path = $dir ? $this->path.$dir.'/' : $this->path;
		$data = directory_map($path, 1);
		$list = array();

		if ($data) {
			foreach ($data as $t) {
				$ext = strrchr($t, '.');
				if ($ext && in_array(strtolower($ext), array('.php'))) {
                    continue;
                }
				if (!$dir && in_array(basename($t), $this->_dir)) {
                    continue;
                }
                $icon = $eurl = '';
                if ($ext) {
                    $ext = strtolower(trim($ext, '.'));
                    $icon = is_file(WEBPATH.'statics/admin/images/ext/'.$ext.'.gif') ? 'ext/'.$ext.'.gif' : 'file.gif';
                    if (in_array($ext, array('html', 'html', 'js', 'css'))) {
                        $eurl = dr_url($this->template->get_value('furi').'edit', array('file' => $dir.'/'.$t));
                    } elseif (in_array($ext, array('jpg', 'gif', 'png'))) {
                        $eurl = ''.str_replace(WEBPATH, SITE_URL, $this->path).trim($dir.'/'.$t, '/').'" target="_blank"';
                    } else {
                        $eurl = 'javascript:;';
                    }
                }
				$list[] = array(
                    'ext' => $ext,
                    'file' => $t,
                    'icon' => $icon,
                    'eurl' => $eurl,
                );
			}
		}

        $this->template->assign(array(
			'dir' => $dir,
			'path' => $path,
			'list' => $list,
			'parent' => dirname($dir),
		));
		$this->template->display('file_index.html');
	}

	/**
     * 移动端模板管理
     */
	public function mobile() {

        $this->_init();

        $this->load->helper('directory');
		$dir = trim(str_replace('.', '', $this->input->get('dir',true)), '/');
		$path = $dir ? $this->path.$dir.'/' : $this->path;
		$data = directory_map($path, 1);
		$list = array();

		if ($data) {
			foreach ($data as $t) {
				$ext = strrchr($t, '.');
				if ($ext && in_array(strtolower($ext), array('.php'))) {
                    continue;
                } elseif (!$dir && in_array(basename($t), $this->_dir)) {
                    continue;
                }
                $icon = $eurl = '';
                if ($ext) {
                    $ext = strtolower(trim($ext, '.'));
                    $icon = is_file(WEBPATH.'statics/admin/images/ext/'.$ext.'.gif') ? 'ext/'.$ext.'.gif' : 'file.gif';
                    if (in_array($ext, array('html', 'html', 'js', 'css'))) {
                        $eurl = dr_url($this->template->get_value('furi').'edit', array('file' => $dir.'/'.$t, 'ismb'=>1));
                    } elseif (in_array($ext, array('jpg', 'gif', 'png'))) {
                        $eurl = ''.str_replace(WEBPATH, SITE_URL, $this->path).trim($dir.'/'.$t, '/').'" target="_blank"';
                    } else {
                        $eurl = 'javascript:;';
                    }
                }
				$list[] = array(
                    'ext' => $ext,
                    'file' => $t,
                    'icon' => $icon,
                    'eurl' => $eurl,
                );
			}
		}

        $this->template->assign(array(
			'dir' => $dir,
			'path' => $path,
			'list' => $list,
			'parent' => dirname($dir),
            'upload' => 'dr_upload_files2(\''.dr_url('api/upload', array('path' => $path)).'\')',
		));
		$this->template->display('file_index.html');
	}

	/**
     * 创建文件或者目录
     */
	public function add() {

        $this->_init();

        $dir = trim(str_replace('.', '', $this->input->get('dir',true)), '/');
		$path = $dir ? $this->path.$dir.'/' : $this->path;
		$error = $file = '';
		!is_dir($path) && exit('<p style="padding:10px 20px 20px 20px">'.fc_lang('文件目录不存在').'</p>');
		
		if (IS_POST) {
			$file = trim(str_replace(array('/', '\\', ';'), '', $this->input->post('file')), '/');
			!$file && exit(dr_json(0, fc_lang('文件或者目录不存在'), 'file'));
			substr_count($file, '.') > 1 && exit(dr_json(0, fc_lang('请确认否是有效的文件'), 'file'));
			is_file($path.$file) && exit(dr_json(0, fc_lang('文件或者目录已经存在了'), 'file'));
			$ext = strrchr($file, '.');
			if ($ext) {
				// 创建文件
				if (in_array($ext, array('.html', '.js', '.css'))) {
					if (file_put_contents($path.$file, '') === FALSE) {
						exit(dr_json(0, fc_lang('文件添加失败，请检查写入权限'), 'file'));
					} else {
                        $this->system_log('创建文件：'.$path.$file); // 记录日志
						exit(dr_json(1, fc_lang('文件添加成功'), 'file'));
					}
				} else {
					exit(dr_json(0, fc_lang('文件扩展名不正确'), 'file'));
				}
			} else {
				// 创建目录
				if (mkdir($path.$file)) {
                    $this->system_log('创建目录：'.$path.$file); // 记录日志
					exit(dr_json(1, fc_lang('目录添加成功'), 'file'));
				} else {
					exit(dr_json(0, fc_lang('目录添加失败，请检查写入权限'), 'file'));
				}
			}

		}
		
		$this->template->display('file_add.html');
	}
	
	/**
     * 修改文件内容
     */
	public function edit() {

        $this->_init();

        $file = trim(str_replace(array('../', '\\', '..'), array('', '/', ''), $this->input->get('file',true)), '/');
        !is_file($this->path.$file) &&  $this->admin_msg(fc_lang('文件不存在'));
        if (!in_array(strtolower(strrchr($file, '.')), array('.html', '.js', '.css'))) {
            $this->admin_msg(fc_lang('文件扩展名不规范'));
        }
		
		if (IS_POST) {
			$code = $this->input->post('code');
			file_put_contents($this->path.$file, $code);
            $this->system_log('修改文件：'.$this->path.$file); // 记录日志
		}
		
		$furi = $this->template->get_value('furi');
		$this->template->assign(array(
			'path' => $this->path.$file,
			'back' => dr_url($furi.($this->input->get('ismb') ? 'mobile' : 'index'), array('dir'=> dirname($file), 'ismb' => $this->input->get('ismb'))),
			'body' => in_array(strtolower(strrchr($file, '.')), array('.js')) ? file_get_contents($this->path.$file) : htmlentities(file_get_contents($this->path.$file), ENT_COMPAT,'UTF-8'),

		));
		$this->template->display('file_edit.html');
		
	}

	/**
     * 解压
     */
	public function jie() {

        $this->_init();

        $file = trim(str_replace(array('../', '\\', '..'), array('', '/'), $this->input->get('file',true)), '/');
		!is_file($this->path.$file) && $this->admin_msg(fc_lang('文件不存在'));

        $path = dirname($this->path.$file);
		!is_dir($path) && $this->admin_msg(fc_lang('文件目录不存在'));

        // 解压缩文件
        $this->load->library('Pclzip');
        $this->pclzip->PclFile($this->path.$file);
        // 覆盖至网站根目录
        $this->pclzip->extract(PCLZIP_OPT_PATH, $path, PCLZIP_OPT_REPLACE_NEWER);

        $this->system_log('解压文件：'.$this->path.$file); // 记录日志
        $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url($this->template->get_value('furi'). ($this->input->get('ismb') ? 'mobile' : 'index'), array('dir'=> dirname($file), 'ismb' => $this->input->get('ismb'))), 1, 2);

	}
	
	/**
     * 删除
     */
	public function del() {

        $this->_init();

        $file = trim(str_replace(array('../', '\\', '..'), array('', '/'), $this->input->get('file',true)), '/');
		!$file && exit(dr_json(0, fc_lang('文件或者目录格式不正确')));
		
		if (is_dir($this->path.$file)) {
			$this->load->helper('file');
			delete_files($this->path.$file, TRUE);
			@rmdir($this->path.$file);
            $this->system_log('删除目录：'.$this->path.$file); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		} else {
			@unlink($this->path.$file);
            $this->system_log('删除文件：'.$this->path.$file); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
	}
	
	/**
     * 标签向导
     */
	public function tag() {

        $this->_init();

		SYS_DEBUG && $this->output->enable_profiler(FALSE);
		
		if (IS_AJAX) {
            $this->load->model('system_model');
            $data = $this->input->post('data');
            $cache = $this->system_model->cache(); // 表结构缓存
			echo '<div style="border: 1px solid #DCE3ED;padding:10px;width:650px;">';
			switch ($this->input->post('action')) {
				case 'navigator':
					echo '<li style="list-style: none;">{list action=navigator type='.$data['type'].((int)$data['num'] ? ' num='.(int)$data['num'] : '').' return='.$data['return'].'}</li>';
					echo '<li style="list-style: none;">当前循环序号：{$key}（从0开始）</li>';
					echo '<li style="list-style: none;">字段调用方式：{$'.$data['return'].'.字段名称}（字段名称下面介绍）</li>';
					echo '<li style="list-style: none;">{/list}</li>';
					echo '<li style="list-style: none;">{$error}返回错误提示代码</li>';
					echo '</div>';
					echo '<div style="border: 1px solid #DCE3ED;margin-top:10px;padding:10px;width:650px;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="200">字段<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix('1_navigator')]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$'.$data['return'].'.'.$t['name'].'}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					break;
				case 'page_list':
					echo '<li style="list-style: none;">{list action=page module='.$data['module'].((int)$data['num'] ? ' num='.(int)$data['num'] : '').' pid='.(int)$data['pid'].' return='.$data['return'].'}</li>';
					echo '<li style="list-style: none;">当前循环序号：{$key}（从0开始）</li>';
					echo '<li style="list-style: none;">字段调用方式：{$'.$data['return'].'.字段名称}（字段名称下面介绍）</li>';
					echo '<li style="list-style: none;">{/list}</li>';
					echo '<li style="list-style: none;">{$error}返回错误提示代码</li>';
					echo '</div>';
					echo '<div style="border: 1px solid #DCE3ED;margin-top:10px;padding:10px;width:650px;max-height:300px;overflow:auto;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="200">字段<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix(SITE_ID.'_page')]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$'.$data['return'].'.'.$t['name'].'}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					break;
				case 'page_show':
					echo '<div style="max-height:400px;overflow:auto;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="400">调用方式<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix(SITE_ID.'_page')]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$ci->get_cache(\'page-'.SITE_ID.'\', \'data\', \''.$data['module'].'\', '.(int)$data['id'].', \''.$t['name'].'\')}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					echo '</div>';
					break;
				case 'linkage_list':
					echo '<li style="list-style: none;">{list action=linkage code='.$data['code'].((int)$data['num'] ? ' num='.(int)$data['num'] : '').' return='.$data['return'].'}</li>';
					echo '<li style="list-style: none;">当前循环序号：{$key}（从0开始）</li>';
					echo '<li style="list-style: none;">字段调用方式：{$'.$data['return'].'.字段名称}（字段名称下面介绍）</li>';
					echo '<li style="list-style: none;">{/list}</li>';
					echo '<li style="list-style: none;">{$error}返回错误提示代码</li>';
					echo '<li style="list-style: none;">知道id显示菜单名字：{dr_linkagepos(\''.$data['code'].'\', ID, \'\')}</li>';
					echo '</div>';
					echo '<div style="border: 1px solid #DCE3ED;margin-top:10px;padding:10px;width:650px;max-height:300px;overflow:auto;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="200">字段<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix('linkage_data_1')]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$'.$data['return'].'.'.$t['name'].'}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					break;
				case 'form_list':
					echo '<li style="list-style: none;">{list action=form form='.$data['form'].((int)$data['num'] ? ' num='.(int)$data['num'] : '').' return='.$data['return'].'}</li>';
					echo '<li style="list-style: none;">当前循环序号：{$key}（从0开始）</li>';
					echo '<li style="list-style: none;">字段调用方式：{$'.$data['return'].'.字段名称}</li>';
					echo '<li style="list-style: none;">字段名称在“系统->系统维护->数据备份”中单击以站点id_form_表单表名称的表就知道了</li>';
					echo '<li style="list-style: none;">{/list}</li>';
					echo '<li style="list-style: none;">{$error}返回错误提示代码</li>';
					echo '<li style="list-style: none;">{$sql}返回这段查询的SQL代码，调试开发期间很有用处</li>';
					echo '</div>';
					break;
				case 'module_list':
					echo '<li style="list-style: none;">{list action=module module='.APP_DIR.((int)$data['num'] ? ' num='.(int)$data['num'] : '').' return='.$data['return'].'}</li>';
					echo '<li style="list-style: none;">当前循环序号：{$key}（从0开始）</li>';
					echo '<li style="list-style: none;">字段调用方式：{$'.$data['return'].'.字段名称}（字段名称下面介绍）</li>';
					echo '<li style="list-style: none;">{/list}</li>';
					echo '<li style="list-style: none;">{$error}返回错误提示代码</li>';
					echo '<li style="list-style: none;">{$sql}返回这段查询的SQL代码，调试开发期间很有用处</li>';
					echo '</div>';
					echo '<div style="border: 1px solid #DCE3ED;margin-top:10px;padding:10px;width:650px;max-height:300px;overflow:auto;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="200">字段<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix(SITE_ID.'_'.APP_DIR)]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$'.$data['return'].'.'.$t['name'].'}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					break;
				case 'category_list':
					echo '<li style="list-style: none;">{list action=category module='.APP_DIR.((int)$data['num'] ? ' num='.(int)$data['num'] : '').' pid='.(int)$data['pid'].' return='.$data['return'].'}</li>';
					echo '<li style="list-style: none;">当前循环序号：{$key}（从0开始）</li>';
					echo '<li style="list-style: none;">字段调用方式：{$'.$data['return'].'.字段名称}（字段名称下面介绍）</li>';
					echo '<li style="list-style: none;">{/list}</li>';
					echo '<li style="list-style: none;">{$error}返回错误提示代码</li>';
					echo '</div>';
					echo '<div style="border: 1px solid #DCE3ED;margin-top:10px;padding:10px;width:650px;max-height:300px;overflow:auto;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="200">字段<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix(SITE_ID.'_'.APP_DIR.'_category')]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$'.$data['return'].'.'.$t['name'].'}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					break;
				case 'category_show':
					echo '<div style="max-height:400px;overflow:auto;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="400">调用方式<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix(SITE_ID.'_'.APP_DIR.'_category')]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$ci->get_cache(\'module-'.SITE_ID.'-'.APP_DIR.'\', \'category\', '.(int)$data['id'].', \''.$t['name'].'\')}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					echo '</div>';
					break;
				case 'member_group':
					echo '<li style="list-style: none;">{list action=cache name=member.group return='.$data['return'].'}</li>';
					echo '<li style="list-style: none;">当前循环序号：{$key}（从0开始）</li>';
					echo '<li style="list-style: none;">字段调用方式：{$'.$data['return'].'.字段名称}（字段名称下面介绍）</li>';
					echo '<li style="list-style: none;">{/list}</li>';
					echo '<li style="list-style: none;">{$error}返回错误提示代码</li>';
					echo '</div>';
					echo '<div style="border: 1px solid #DCE3ED;margin-top:10px;padding:10px;width:650px;max-height:300px;overflow:auto;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="200">字段<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix('member_group')]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$'.$data['return'].'.'.$t['name'].'}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					break;
				case 'member_level':
					echo '<li style="list-style: none;">{list action=cache name=member.group.'.(int)$data['gid'].'.level return='.$data['return'].'}</li>';
					echo '<li style="list-style: none;">当前循环序号：{$key}（从0开始）</li>';
					echo '<li style="list-style: none;">等级星星调用：{dr_show_stars($'.$data['return'].'.stars)}</li>';
					echo '<li style="list-style: none;">字段调用方式：{$'.$data['return'].'.字段名称}（字段名称下面介绍）</li>';
					echo '<li style="list-style: none;">{/list}</li>';
					echo '<li style="list-style: none;">{$error}返回错误提示代码</li>';
					echo '</div>';
					echo '<div style="border: 1px solid #DCE3ED;margin-top:10px;padding:10px;width:650px;max-height:300px;overflow:auto;">';
					echo '<table width="100%"><tbody>';
					echo '<tr><th width="200">字段<br></th><th align="left">说明</th></tr>';
					$table = $cache[$this->db->dbprefix('member_level')]['field'];
					if ($table) {
						foreach ($table as $t) {
							echo '<tr><td>{$'.$data['return'].'.'.$t['name'].'}<br></td><td align="left">'.$t['note'].'</td></tr>';
						}
					}
					echo '</tbody></table>';
					break;
				default:
					echo '未知操作';
					break;
			}
			echo '</div>';
			
		} else {
			
			switch (APP_DIR) {
				case '':
					$nav = explode(',', SITE_NAVIGATOR);
					$navigator = array();
					foreach ($nav as $i => $name) {
						if ($name) {
							$navigator[$i] = $name;
						}
					}
					$this->template->assign(array(
						'form' => $this->db
									   ->get(SITE_ID.'_form')
									   ->result_array(),
						'linkage' => $this->db
										  ->order_by('id ASC')
										  ->get('linkage')
										  ->result_array(),
						'navigator' => $navigator,
					));
					$tpl = '';
					break;
				case 'member':
					$tpl = '_member';
					break;
				default:
					$tpl = '_module';
					break;
			}
			
			$this->template->assign(array(
				'return_var' => 't',
				'navigator' => $navigator,
			));
			$this->template->display('file_tag'.$tpl.'.html');
		}
	}

    public function store() {
        exit('接口关闭');
    }
}