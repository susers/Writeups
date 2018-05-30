<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Linkage_model extends M_Model {

	private	$categorys;

	/**
	 * 联动菜单数据
	 *
	 * @param	intval	$id
	 * @return	array
	 */
	public function get($id) {
		return $this->db->where('id', $id)->get('linkage')->row_array();
	}
	
	/**
	 * 联动子菜单数据
	 *
	 * @param	intval	$id
	 * @param	intval	$key	顶级菜单id
	 * @return	array
	 */
	public function gets($id, $key) {
		return $this->db->where('id', $id)->get('linkage_data_'.$key)->row_array();
	}
	
	/**
	 * 全部名称数据
	 *
	 * @return	array
	 */
	public function get_data() {
		return $this->db->order_by('id ASC')->get('linkage')->result_array();
	}
	
	/**
	 * 全部子菜单数据
	 *
	 * @param	array	$link
	 * @param	intval	$pid
	 * @return	array
	 */
	public function get_list_data($link, $pid = NULL) {

		$key = (int)$link['id'];
		$data = array();

		if ($link['type'] == 1) {
            $this->db->where('site', SITE_ID);
        }

		if ($pid === NULL) {
			$_data = $this->db->order_by('displayorder ASC,id ASC')->get('linkage_data_'.$key)->result_array();
		} else {
			$_data = $this->db->where('pid', (int)$pid)->order_by('displayorder ASC,id ASC')->get('linkage_data_'.$key)->result_array();
		}

		if (!$_data) {
            return $data;
        }

		foreach ($_data as $t) {
			$data[$t['id']]	= $t;
		}

		return $data;
	}
	
	/**
	 * 添加
	 *
	 * @param	array	$data
	 * @return	intval
	 */
	public function add($data) {

		if (!$data || !$data['name']) {
            return array('error' => fc_lang('联动菜单名称不能为空'), 'name' => 'name');
        } elseif ($this->code_exitsts($data['code'])) {
            return array('error' => fc_lang('联动菜单不存在!'), 'name' => 'code');
        }

		$this->db->insert('linkage', array(
			'name' => $data['name'],
			'code' => $data['code'],
			'type' => $data['type'],
		));

		$id = $this->db->insert_id();
		$table = $this->db->dbprefix('linkage_data_'.$id);
		$this->db->query('DROP TABLE IF EXISTS `'.$table.'`');
		$this->db->query(trim("CREATE TABLE IF NOT EXISTS `{$table}` (
		  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
		  `site` smallint(5) unsigned NOT NULL,
		  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
		  `pids` varchar(255) DEFAULT NULL COMMENT '所有上级id',
		  `name` varchar(30) NOT NULL COMMENT '菜单名称',
		  `cname` varchar(255) NOT NULL COMMENT '菜单别名',
		  `child` tinyint(1) unsigned DEFAULT NULL DEFAULT '0' COMMENT '是否有下级',
		  `hidden` tinyint(1) unsigned DEFAULT NULL DEFAULT '0' COMMENT '前端隐藏',
		  `childids` text DEFAULT NULL COMMENT '下级所有id',
		  `displayorder` tinyint(3) DEFAULT NULL DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `cname` (`cname`),
		  KEY `hidden` (`hidden`),
		  KEY `list` (`site`,`displayorder`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='联动菜单数据表'"));

		return $id;
	}
	
	/**
	 * 修改
	 *
	 * @param	array	$_data
	 * @param	array	$data
	 * @return	string
	 */
	public function edit($id, $data) {

		if (!$id) {
            return array('error' => fc_lang('对不起，数据被删除或者查询不存在'), 'name' => 'name');
        } elseif (!$data || !$data['name']) {
            return array('error' => fc_lang('联动菜单名称不能为空'), 'name' => 'name');
        } elseif ($this->code_exitsts($data['code'], $id)) {
            return array('error' => fc_lang('联动菜单不存在!'), 'name' => 'code');
        }

		$this->db->where('id', $id)->update('linkage', array(
			'name' => $data['name'],
			'code' => $data['code'],
			'type' => $data['type'],
		));

		return NULL;
	}
	
	/**
	 * 标示是否存在
	 *
	 * @param	array	$data
	 * @return	bool
	 */
	private function code_exitsts($code, $id = 0) {
		return $code ? $this->db->where('code', $code)->where('id<>', $id)->count_all_results('linkage') : 1;
	}
	
	/**
	 * 批量添加
	 *
	 * @param	array	$key
	 * @param	array	$data
	 * @return	
	 */
	public function adds($key, $data) {
	
		if (!$key) {
            return array('error' => fc_lang('联动菜单不存在!'), 'name' => 'name');
        } elseif (!$data) {
            return array('error' => fc_lang('联动菜单名称不能为空'), 'name' => 'name');
        }

        if ($_POST['_all']) {
            $names = explode(PHP_EOL, trim($data['names']));
            if (!$names) {
                return array('error' => fc_lang('联动菜单名称不能为空'), 'name' => 'names');
            }
            foreach ($names as $t) {
                $t = trim($t);
                if (!$t) {
                    continue;
                }
                $cname = dr_word2pinyin($t);
                $num = $this->db->where('cname', $cname)->count_all_results('linkage_data_'.$key);
                $this->db->insert('linkage_data_'.$key, array(
                    'pid' => (int)$data['pid'],
                    'name' => $t,
                    'site' => SITE_ID,
                    'cname' => $num ? $cname.$num : $cname,
                    'displayorder' => (int)$data['displayorder']
                ));
            }
        } else {
            $name = $data['name'];
            if (!$name) {
                return array('error' => fc_lang('联动菜单名称不能为空'), 'name' => 'name');
            }
            $cname = $data['cname'] ? $data['cname'] : '';
            $this->db->insert('linkage_data_'.$key, array(
                'pid' => (int)$data['pid'],
                'name' => $name,
                'site' => SITE_ID,
                'cname' => $cname,
                'displayorder' => (int)$data['displayorder']
            ));
            if (!$cname) {
                $id = $this->db->insert_id();
                $this->db->where('id', $id)->update('linkage_data_'.$key, array(
                    'cname' => dr_word2pinyin($name).''.$id,
                ));
            }
        }

		$this->repair($key);
		
		return NULL;
	}
	
	/**
	 * 修改
	 *
	 * @param	array	$data
	 * @param	array	$_data
	 * @return	
	 */
	public function edits($key, $id, $data) {

		if (!$data || !$data['name']) {
            return array('error' => fc_lang('联动菜单名称不能为空'), 'name' => 'name');
        } elseif ($this->db->where('id<>', (int)$id)->where('cname', $data['cname'])->count_all_results('linkage_data_'.$key)) {
            return array('error' => fc_lang('别名已经存在,不能重复'), 'name' => 'cname');
        } elseif (is_numeric($data['cname']) && $data['cname'] != $id) {
            return array('error' => fc_lang('别名不能是数字'), 'name' => 'cname');
        }

		$this->db->where('id', (int)$id)->update('linkage_data_'.$key, array(
			'pid' => (int)$data['pid'],
			'name' => $data['name'],
			'cname' => $data['cname'],
			'displayorder' => (int)$data['displayorder']
		));
		$this->repair($key);

		return NULL;
	}
	
	/**
	 * 获取父栏目ID列表
	 * 
	 * @param	integer	$catid	栏目ID
	 * @param	array	$pids	父目录ID
	 * @param	integer	$n		查找的层次
	 * @return	string
	 */
	private function get_pids($catid, $pids = '', $n = 1) {

		if ($n > 10 || !is_array($this->categorys)
            || !isset($this->categorys[$catid])) {
            return FALSE;
        }

		$pid = $this->categorys[$catid]['pid'];
		$pids = $pids ? $pid.','.$pids : $pid;
		$pid ? $pids = $this->get_pids($pid, $pids, ++$n) : $this->categorys[$catid]['pids'] = $pids;

		return $pids;
	}
	
	/**
	 * 获取子栏目ID列表
	 * 
	 * @param	$catid	栏目ID
	 * @return	string
	 */
	private function get_childids($catid, $n = 1) {
		$childids = $catid;
        if ($n > 10 || !is_array($this->categorys)
            || !isset($this->categorys[$catid])) {
            return $childids;
        }
		if (is_array($this->categorys)) {
			foreach ($this->categorys as $id => $cat) {
				// 避免造成死循环
				$cat['pid']
				&& $id != $catid
				&& $cat['pid'] == $catid
				&& $this->categorys[$catid]['pid'] != $id
				&& $childids.= ','.$this->get_childids($id, ++$n);
			}
		}
		return $childids;
	}
	
	/**
	 * 找出子目录列表
	 *
	 * @param	array	$data
	 * @return	bool
	 */
	private function get_categorys($data = array()) {

		if (is_array($data) && !empty($data)) {
			foreach ($data as $catid => $c) {
				$this->categorys[$catid] = $c;
				$result = array();
				foreach ($this->categorys as $_k => $_v) {
					$_v['pid'] && $result[] = $_v;
				}
			}
		}

		return true;
	}
	
	/**
     * 修复菜单数据
	 */
	public function repair($key) {

		if (!$key) {
            return NULL;
        }

		$table = 'linkage_data_'.$key;
		$_data = $this->db->order_by('displayorder ASC,id ASC')->get($table)->result_array();
		if (!$_data) {
            return NULL;
        }

        $this->categorys = $categorys = array();

        // 全部栏目数据
        foreach ($_data as $t) {
            $categorys[$t['id']] = $this->categorys[$t['id']] = $t;
            // 归类
            $this->pids[$t['pid']][] = $t['id'];
        }

        foreach ($this->categorys as $catid => $cat) {
            $this->categorys[$catid]['pids'] = $this->get_pids($catid);
            $this->categorys[$catid]['childids'] = $this->get_childids($catid);
            $this->categorys[$catid]['child'] = is_numeric($this->categorys[$catid]['childids']) ? 0 : 1;
            // 当库中与实际不符合才更新数据表
            ($categorys[$catid]['pids'] != $this->categorys[$catid]['pids']
                || $categorys[$catid]['childids'] != $this->categorys[$catid]['childids']
                || $categorys[$catid]['child'] != $this->categorys[$catid]['child'])
            && $this->db->where('id', $cat['id'])->update($table, array(
                'pids' => $this->categorys[$catid]['pids'],
                'child' => $this->categorys[$catid]['child'],
                'childids' => $this->categorys[$catid]['childids']
            ));
        }

        /*
		
		foreach ($_data as $t) {
			$categorys[$t['id']] = $t;
		}
		
		$this->categorys = $categorys; // 全部栏目数据
		$this->get_categorys($categorys); // 查找子目录

		if (is_array($this->categorys)) {
			foreach ($this->categorys as $catid => $cat) {
				$pids = $this->get_pids($catid);
				$childids = $this->get_childids($catid);
				$child = is_numeric($childids) ? 0 : 1;
				if ($categorys[$catid]['pids'] != $pids 
				|| $categorys[$catid]['childids'] != $childids 
				|| $categorys[$catid]['child'] != $child) {
					// 当库中与实际不符合才更新数据表
					$this->db->where('id', $cat['id'])->update($table, array(
						'pids' => $pids,
						'child' => $child,
						'childids' => $childids
					));
				}
			}
		}*/
	}
	
	/**
     * 缓存
	 */
	public function cache($siteid = SITE_ID) {

		$linkage = $this->get_data();
		if (!$linkage) {
            return NULL;
        }

		$id = $level = array();
		foreach ($linkage as $link) {
			$this->repair($link['id']);
			$cid = $data = $lv = array();
			$table = 'linkage_data_'.$link['id'];
			
			// 站点独立 // 全局共享
			$list = $link['type'] ? $this->db->where('site', $siteid)->order_by('displayorder ASC,id ASC')->get($table)->result_array() : $this->db->order_by('displayorder ASC,id ASC')->get($table)->result_array();
			
			if ($list) {
				foreach ($list as $t) {
					$lv[] = substr_count($t['pids'], ',');
                    $t['ii'] = $t['id'];
                    $t['id'] = $t['cname'];
                    $cid[$t['ii']] = $t['id'];
					$data[$t['cname']] = $t;
				}
			}
			
            $id[$link['code']] = $link['id'];
			$level[$link['code']] = $lv ? max($lv) : 0;
			$this->dcache->set('linkage-'.$siteid.'-'.$link['code'], $data);
			$this->dcache->set('linkage-'.$siteid.'-'.$link['code'].'-id', $cid);
		}

        $this->dcache->set('linkid-'.$siteid, $id);
		$this->dcache->set('linklevel-'.$siteid, $level);

	}
}