<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

 /* v3.1.0  */

/**
 * 树形结构
 */ 

class Dtree {

	private $i = 0;
	private $count = 0;

	public $ret;
	public $arr; // 生成树型结构所需要的2维数组
	public $nbsp = "&nbsp;";
	public $icon = array('│', '├', '└'); // 生成树型结构所需修饰符号，可以换成图片

	/**
	 * 构造函数，初始化类
	 */
	public function init($arr = array()) {

		$this->ret = '';
		$this->arr = $arr;
		
	    return is_array($arr);
	}

    /**
	 * 得到父级数组
	 *
	 * @param int
	 * @return array
	 */
	public function get_parent($myid) {
	
		$newarr = array();
		
		if(!isset($this->arr[$myid])) return FALSE;
		
		$pid = $this->arr[$myid]['pid'];
		$pid = $this->arr[$pid]['pid'];
		
		if (is_array($this->arr)) {
			foreach ($this->arr as $id => $a) {
				if ($a['pid'] == $pid) $newarr[$id] = $a;
			}
		}
		
		return $newarr;
	}

    /**
	 * 得到子级数组
	 *
	 * @param int
	 * @return array
	 */
	public function get_child($myid) {
	
		$a = $newarr = array();
		
		if (is_array($this->arr)) {
			foreach ($this->arr as $id => $a) {
				if ($a['pid'] == $myid) $newarr[$id] = $a;
			}
		}
		
		return $newarr ? $newarr : FALSE;
	}

    /**
	 * 得到当前位置数组
	 *
	 * @param int
	 * @return array
	 */
	public function get_pos($myid,&$newarr) {
	
		$a = array();
		
		if (!isset($this->arr[$myid])) return FALSE;
		
		$pid = $this->arr[$myid]['pid'];
        $newarr[] = $this->arr[$myid];
		
		if (isset($this->arr[$pid])) $this->get_pos($pid, $newarr);
		
		if (is_array($newarr)) {
			krsort($newarr);
			foreach ($newarr as $v) {
				$a[$v['id']] = $v;
			}
		}
		
		return $a;
	}

    /**
	 * 得到树型结构
     * 
	 * @param int ID，表示获得这个ID下的所有子级
     * @param string 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
	 * @param int 被选中的ID，比如在做树型下拉框的时候需要用到
  	 * @return string
	 */
	public function get_tree($myid, $str, $sid = 0, $adds = '', $str_group = '') {
		
		$child = $this->get_child($myid);
		$number = 1;
		
		if (is_array($child)) {
		    $total = count($child);
			foreach ($child as $id => $value) {
			
				$j = $k = '';
                $class = 'dr_catid_'.$value['id'];
                $parent = !$value['child'] ? '' : '<a href="javascript:void();" class="blue select-cat" childs="'.$value['childids'].'" action="open" catid='.$id.'>[-]</a>&nbsp;';
				if ($number == $total) {
					$j.= $this->icon[2];
				} else {
					$j.= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';
				$selected = $id == $sid ? 'selected' : '';
				
				@extract($value);
				
				$pid == 0 && $str_group ? @eval("\$nstr = \"$str_group\";") : @eval("\$nstr = \"$str\";");
				$this->ret.= $nstr;
				$nbsp = $this->nbsp;
				$this->get_tree($id, $str, $sid, $adds.$k.$nbsp, $str_group);
				$number++;
				
			}
		}
		return $this->ret;
	}
    
    /**
	 * 同上一方法类似,但允许多选
	 */
	public function get_tree_multi($myid, $str, $sid = 0, $adds = '') {
	
		$child = $this->get_child($myid);
		$number = 1;
		
		if (is_array($child)) {
		    $total = count($child);
			foreach ($child as $id => $a) {
			
				$j = $k = '';
				if ($number == $total) {
					$j.= $this->icon[2];
				} else {
					$j.= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';
				$selected = $this->have($sid, $id) ? 'selected' : '';
				
				@extract($a);

                @eval("\$nstr = \"$str\";");
				$this->ret.= $nstr;
				$this->get_tree_multi($id, $str, $sid, $adds.$k.'&nbsp;');
				$number++;
				
			}
		}
		
		return $this->ret;
	}
	
	/**
	 * 用于栏目选择框
	 * 
	 * @param integer	$myid	要查询的ID
	 * @param string	$str	第一种HTML代码方式
	 * @param string	$str2	第二种HTML代码方式
	 * @param integer	$sid	默认选中
	 * @param integer	$adds	前缀
	 */
	public function get_tree_category($myid, $str, $str2, $sid = 0, $adds = '') {

		$child = $this->get_child($myid);
		$number = 1;

		$this->count++;
		if ($this->count > 3500) {
			return; // 防止死循环
		}

		if (is_array($child)) {
			
		    $total = count($child);
			foreach ($child as $id => $a) {
			
				$j = $k = '';
				if ($number == $total) {
					$j.= $this->icon[2];
				} else {
					$j.= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				
				$spacer = $adds ? $adds.$j : '';
				
				$selected = $this->have($sid, $id) ? 'selected' : '';
				@extract($a);
				
				$now = $this->get_child($id);
				if (!$now && $html_disabled) continue;

				if (empty($html_disabled)) {
                    @eval("\$nstr = \"$str\";");
				} else {
                    @eval("\$nstr = \"$str2\";");
				}
				
				$this->ret.= $nstr;
				$this->get_tree_category($id, $str, $str2, $sid, $adds.$k.'&nbsp;');
				$number++;
			}
		}
		
		return $this->ret;
	}
	
	/**
	 * 获取子栏目json
	 *
	 * Enter description here ...
	 * @param unknown_type $myid
	 */
	public function creat_sub_json($myid, $str = '') {
	
		$n = 0;
		$sub_cats = $this->get_child($myid);
		
		if (is_array($sub_cats)) foreach($sub_cats as $c) {			
			$data[$n]['id'] = iconv(CHARSET,'utf-8', $c['catid']);
			if ($this->get_child($c['catid'])) {
				$data[$n]['text'] = iconv(CHARSET, 'utf-8', $c['catname']);
				$data[$n]['classes'] = 'folder';
				$data[$n]['liclass'] = 'hasChildren';
				$data[$n]['children'] = array(array('text' => '&nbsp;', 'classes' => 'placeholder'));
			} else {				
				if ($str) {
					@extract(array_iconv($c, CHARSET, 'utf-8'));
                    @eval("\$data[$n]['text'] = \"$str\";");
				} else {
					$data[$n]['text'] = iconv(CHARSET, 'utf-8', $c['catname']);
				}
			}
			$n++;
		}
		return json_encode($data);		
	}
	
	private function have($list, $item){
		return(strpos(',,'.$list.',', ','.$item.','));
	}
}