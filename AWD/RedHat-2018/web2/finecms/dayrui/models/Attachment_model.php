<?php


/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

	
class Attachment_model extends M_Model {

    public $siteid;
    public $domain;

	/**
	 * 附件操作模型类
	 */
    public function __construct() {
        parent::__construct();
        $this->siteid = (int)$this->session->userdata('siteid');
        $this->siteid = $this->siteid ? $this->siteid : SITE_ID;
    }
	
    /**
	 * 会员附件
	 *
	 * @param	intval	$uid	uid
	 * @return	array
	 */
    public function limit($uid, $page, $pagesize, $ext, $table) {

    	$sql = ' `'.$this->db->dbprefix('attachment').'` AS `a`,`'.$this->db->dbprefix('attachment_'.(int)substr((string)$uid, -1, 1)).'` AS `b`';
    	$sql.= ' WHERE (`a`.`id`=`b`.`id` AND `a`.`siteid`='.$this->siteid.' AND `a`.`uid`='.$uid.')';
    	if ($ext) {
			$data = explode(',', $ext);
			$where = array();
			foreach ($data as $e) {
				$where[] = '`b`.`fileext`="'.$e.'"';
			}
			$sql.= ' AND ('.implode(' OR ', $where).')';
		}
		$table && $sql.= ' AND `b`.`related` LIKE "'.$this->db->dbprefix($this->siteid.'_'.$table).'-%"';

		$data = $this->db->query('SELECT count(*) as total FROM'.$sql)->row_array();
		$total = (int)$data['total'];

		$sql.= ' ORDER BY `b`.`inputtime` DESC LIMIT '. $pagesize * ($page - 1).','.$pagesize;

		$data = $this->db->query('SELECT * FROM'.$sql)->result_array();
		
		return array($total, $this->_get_format_data($data));
    }
    
    /**
	 * Api附件
	 *
	 * @param	intval	$uid	uid
	 * @param	string	$ext	扩展
	 * @param	intval	$total	总数
	 * @param	intval	$page	当前页
	 * @return	array
	 */
    public function limit_page($uid, $ext, $total, $page) {
    	
    	$sql = 'FROM `'.$this->db->dbprefix('attachment').'` AS `a`,`'.$this->db->dbprefix('attachment_'.(int)substr((string)$uid, -1, 1)).'` AS `b` ';
    	$sql.= 'WHERE (`a`.`id`=`b`.`id` AND `a`.`siteid`='.$this->siteid.' AND `a`.`uid`='.$uid.')';
    	
    	if ($ext) {
			$data = explode('|', $ext);
			$where = array();
			foreach ($data as $e) {
				$where[] = '`b`.`fileext`="'.$e.'"';
			}
			$sql.= ' AND ('.implode(' OR ', $where).')';
		}
    	
    	if (!$total) {
			$data = $this->db->query('SELECT count(*) as total '.$sql)->row_array();
			$total = (int)$data['total'];
			if (!$total) {
                return array(array(), 0);
            }
		}
		
		$sql.= ' ORDER BY `b`.`inputtime` DESC LIMIT '. 7 * ($page - 1).',7';
		
		$data = $this->db->query('SELECT * '.$sql)->result_array();
		
		return array($this->_get_format_data($data), $total);
    }

	public function replace_attach($uid, $related, $attach) {

	}
	
	/**
	 * 更新时的删除附件
	 *
	 * @param	intval	$uid		uid	用户id
	 * @param	string	$related	当前关联字符串
	 * @param	intval	$id			id	附件id
	 * @return	NULL
	 */
	public function delete_for_handle($uid, $related, $id) {
	
		if (!$id || !$uid) {
            return NULL;
        }
		
		// 查询附件
		$data = $this->db->where('id', $id)->get('attachment')->row_array();
		
		// 判断附件归属权限
		if ($related != $data['related']) {
            return NULL;
        }

		// 删除附件数据
		$this->db->delete('attachment', 'id='.(int)$id);
		
		// 查询附件附表
		$tableid = (int)$data['tableid'];
		$info = $this->db->select('attachment,remote')->where('id', (int)$id)->get('attachment_'.$tableid)->row_array();
		if (!$info) {
            return NULL;
        }
		
		// 删除附件文件
		$info['id'] = $id;
		$info['tableid'] = $tableid;
		$this->_delete_attachment($info);
		
		return TRUE;
	}
	
	/**
	 * 删除附件
	 *
	 * @param	intval	$uid		uid	用户id
	 * @param	string	$related	当前关联字符串
	 * @param	intval	$id			id	附件id
	 * @return	NULL
	 */
	public function delete($uid, $related, $id) {
	
		if (!$id || !$uid) {
            return NULL;
        }

		// 查询附件
		$data = $this->db->select('tableid,related')->where('id', $id)->get('attachment')->row_array();
        if (!$data) {
            return NULL;
        }

		// 删除附件数据
		$this->db->delete('attachment', 'id='.(int)$id);
		
		// 查询附件附表
		$tableid = (int)$data['tableid'];
		$info = $this->db->select('attachment,remote')->where('id', (int)$id)->get('attachment_'.$tableid)->row_array();
		if (!$info) {
            return NULL;
        }
		
		// 删除附件文件
		$info['id'] = $id;
		$info['tableid'] = $tableid;
		$this->_delete_attachment($info);
		
		return TRUE;
	}
	

	
	/**
	 * 按表删除附件
	 *
	 * @param	string	$related	相关表标识
	 * @param	intval	$is_all		是否全部表附件
	 * @return	NULL
	 */
	public function delete_for_table($related, $is_all = FALSE) {
		
		if (!$related) {
            return NULL;
        }
		
		$data = $is_all 
			? $this->db->query('select id,tableid from `'.$this->db->dbprefix('attachment').'` where `related` like "%'.$related.'%"')->result_array() 
			: $this->db->select('id,tableid')->where('related', $related)->get('attachment')->result_array();
		
		if (!$data) {
            return NULL;
        }
		
		// 删除附件
		foreach ($data as $t) {
		
            if (!isset($t['id'])) {
                continue;
            }
			
			$this->db->delete('attachment', 'id='.$t['id']);
			
			$info = $this->db->select('attachment,remote')->where('id', $t['id'])->get('attachment_'.(int)$t['tableid'])->row_array();
			if (!$info) {
                return NULL;
            }
			
			$info['id'] = $t['id'];
			$info['tableid'] = $t['tableid'];
			$this->_delete_attachment($info);
		}
		
		return 1;
	}
	
	/**
	 * 按站点删除附件
	 *
	 * @param	intval	$siteid	站点id
	 * @return	NULL
	 */
	public function delete_for_site($siteid) {
		
		if (!$siteid) {
            return NULL;
        }
		
		$data = $this->db->select('id,tableid')->where('siteid', $siteid)->get('attachment')->result_array();
		if (!$data) {
            return NULL;
        }
		
		// 删除附件
		foreach ($data as $t) {
		
			$this->db->delete('attachment', 'id='.$t['id']);
			$info = $this->db->select('attachment,remote')->where('id', $t['id'])->get('attachment_'.(int)$t['tableid'])->row_array();
			if (!$info) {
                continue;
            }
			
			$info['id'] = $t['id'];
			$info['tableid'] = $t['tableid'];
			$this->_delete_attachment($info);
		}

		// 删除附件
		foreach ($data as $t) {
			$this->_delete_attachment($t);
		}
	}
	
	/**
	 * 按会员删除附件
	 *
	 * @param	intval	$siteid	站点id
	 * @return	NULL
	 */
	public function delete_for_uid($uid) {
		
		if (!$uid) {
            return NULL;
        }
		
		$data = $this->db->select('id,tableid')->where('uid', $uid)->get('attachment')->result_array();
		if (!$data) {
            return NULL;
        }
		
		// 删除附件
		foreach ($data as $t) {
			
			$this->db->delete('attachment', 'id='.$t['id']);
			
			$info = $this->db->select('attachment,remote')->where('id', $t['id'])->get('attachment_'.$t['tableid'])->row_array();
			if (!$info) {
                continue;
            }
			
			$info['id'] = $t['id'];
			$info['tableid'] = $t['tableid'];
			$this->_delete_attachment($info);
		}
		
		// 删除附件
		foreach ($data as $t) {
			$this->_delete_attachment($t);
		}
	}
	

	/**
	 * 下载远程文件
	 *
	 * @param	intval	$uid	uid	用户id
	 * @param	string	$url	文件url
	 * @return	array
	 */
	public function catcher($uid, $url) {
	
		if (!$uid || !$url) {
            return NULL;
        }

        if (!$this->domain) {
            // 站点信息
            $siteinfo = $this->ci->get_cache('siteinfo', $this->siteid);
            // 域名验证
            $this->domain = require WEBPATH.'config/domain.php';
            foreach ($siteinfo['remote'] as $t) {
                $this->domain[$t['SITE_ATTACH_URL']] = TRUE;
            }
            $this->domain['baidu.com'] = TRUE;
            $this->domain['google.com'] = TRUE;
        }
		
		foreach ($this->domain as $uri => $t) {
			if (stripos($url, $uri) !== FALSE) {
				return NULL;
			}
		}

		$path = SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
		!is_dir($path) && dr_mkdirs($path);
		
		$filename = substr(md5(time()), 0, 7).rand(100, 999);
		$data = dr_catcher_data($url);
		if (!$data) {
            return NULL;
        }
		
		$fileext = strtolower(trim(substr(strrchr($url, '.'), 1, 10))); //扩展名
		if (file_put_contents($path.$filename.'.'.$fileext, $data)) {
			$info = array(
				'file_ext' => '.'.$fileext,
				'full_path' => $path.$filename.'.'.$fileext,
				'file_size' => filesize($path.$filename.'.'.$fileext)/1024,
				'client_name' => $url,
			);
			return $this->upload($uid, $info, NULL);
		}
		
		return NULL;
	}


	/**
	 * 上传
	 *
	 * @param	intval	$uid	uid	用户id
	 * @param	array	$info	ci 文件上传成功返回数据
     * @param	intval	$id	id	指定附件id
	 * @return	array
	 */
	public function upload($uid, $info, $id = 0) {

		$_ext = strtolower(substr($info['file_ext'], 1));
		$author = $this->_get_member_name($uid);
        $replace = 0;
        $content = @file_get_contents($info['full_path']);

        // 附件信息
        $attachinfo = array();
        list($attachinfo['width'], $attachinfo['height']) = @getimagesize($info['full_path']);


        $tableid = (int)substr((string)$uid, -1, 1);
        $this->db->replace('attachment', array(
            'uid' => (int)$uid,
            'author' => $author,
            'siteid' => $this->siteid,
            'tableid' => $tableid,
            'related' => '',
            'fileext' => $_ext,
            'filemd5' => $content ? md5($content) : 0,
            'download' => 0,
            'filesize' => $info['file_size'] * 1024,
        ));
        $id = $this->db->insert_id();
        // 入库失败，返回错误且删除附件
        if (!$id) {
            @unlink($info['full_path']);
            return fc_lang('文件入库失败，请重试');
        }


		// 存储处理
		$remote = 0;
		$attachment = trim(substr($info['full_path'], strlen(SYS_UPLOAD_PATH)), '/'); // 附件储存地址
        $file = (SYS_UPLOAD_DIR ? SYS_UPLOAD_DIR.'/' : '').$attachment; // 附件网站上的路径


        // 非远程附件补全本地地址
        $file = !$remote ? SYS_ATTACHMENT_URL.$attachment : $file;

		$pos = strrpos($info['client_name'], '.');
		$filename = strpos($info['client_name'], 'http://') === 0 ? trim(strrchr($info['client_name'], '/'), '/') : $info['client_name'];
		$filename = $pos ? substr($filename, 0, $pos) : $filename;


            // 更新替换已使用的附件表
            $this->db->where('id', $id)->replace('attachment_'.(int)$tableid, array(
                'id' => $id,
                'uid' => $uid,
                'author' => $author,
                'remote' => 0,
                'related' => '',
                'inputtime' => SYS_TIME,
                'fileext' => $_ext,
                'filename' => $filename,
                'filesize' => $info['file_size'] * 1024,
                'attachment' => $attachment,
            ));
            $this->ci->clear_cache('attachment-'.$id);

		return array($id, $file, $_ext);
	}
	
	// 会员名称
	private function _get_member_name($uid) {
		$data = $this->db->where('uid', $uid)->select('username')->get('member')->row_array();
		return isset($data['username']) ? $data['username'] : '';
	}
	
	// 格式化输出数据
	private function _get_format_data($data) {
		
		if (!$data) {
            return NULL;
        }
		
		foreach ($data as $i => $t) {
			$data[$i]['ext'] = $t['fileext'];
			$data[$i]['attachment'] = $t['remote'] ? $this->ci->get_cache('attachment', $this->siteid, 'data', $t['remote'], 'url').'/'.$t['attachment'] : dr_file(dr_ck_attach($t['attachment']));
			if (in_array($t['fileext'], array('jpg', 'gif', 'png'))) {
				$data[$i]['show'] = $data[$i]['attachment'];
				$data[$i]['icon'] = THEME_PATH.'admin/images/ext/jpg.gif';
			} else {
				$data[$i]['show'] = is_file(WEBPATH.'statics/admin/images/ext/'.$t['fileext'].'.png') ? THEME_PATH.'admin/images/ext/'.$t['fileext'].'.png' : THEME_PATH.'admin/images/ext/blank.png';
				$data[$i]['icon'] = is_file(WEBPATH.'statics/admin/images/ext/'.$t['fileext'].'.gif') ? THEME_PATH.'admin/images/ext/'.$t['fileext'].'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
			}
			$data[$i]['size'] = dr_format_file_size($t['filesize']);
		}
		
		return $data;
	}

	// 删除文件
	public function _delete_attachment($info) {

			// 删除本地文件
			$file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
			@unlink($file);

		isset($info['tableid']) && $this->db->delete('attachment_'.(int)$info['tableid'], 'id='.(int)$info['id']);

		// 清空附件缓存
		$this->ci->clear_cache('attachment-'.$info['id']);
	}
}