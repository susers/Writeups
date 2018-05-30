<?php



class F_Linkage extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('联动菜单') : ''; // 字段名称
		$this->fieldtype = array(
			'mediumint' => 8
		); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'mediumint'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
		$linkage = isset($option['linkage']) ? $option['linkage'] : '';
		$str = '<select class="form-control" name="data[setting][option][linkage]">';
		$data = $this->ci->db->get('linkage')->result_array();
		if ($data) {
			foreach ($data as $t) {
				$str.= '<option value="'.$t['code'].'" '.($linkage == $t['code'] ? 'selected' : '').'> '.$t['name'].' </option>';
			}
		}
		$str.= '</select>';
		return '<div class="form-group">
                  	<label class="col-md-2 control-label">'.fc_lang('选择菜单').'：</label>
                    <div class="col-md-9"><label>'.$str.'</label></div>
                </div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('默认值').'：</label>
				<div class="col-md-9">
					<label><input id="field_default_value" type="text" class="form-control" size="20" value="'.$option['value'].'" name="data[setting][option][value]"></label>
					<label>'.$this->member_field_select().'</label>
					<span class="help-block">'.fc_lang('也可以设置会员表字段，表示用当前登录会员信息来填充这个值').'</span>
				</div>
			</div>
				';
	}
	
	/**
	 * 创建sql语句
	 */
	public function create_sql($name, $option) {
		$sql = 'ALTER TABLE `{tablename}` ADD `'.$name.'` mediumint( 8 ) UNSIGNED NULL';
		return $sql;
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
		return $value;
	}
	
	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	string	$value	值
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
		// 表单宽度设置
		$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '150';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 联动菜单缓存
		$linkage = $this->ci->get_cache('linkage-'.SITE_ID.'-'.$cfg['option']['linkage']);
        $linklevel = $this->ci->get_cache('linklevel-'.SITE_ID);
		$linkageid = $this->ci->get_cache('linkage-'.SITE_ID.'-'.$cfg['option']['linkage'].'-id');
		// 
		$value = $value ? $value : $this->get_default_value($cfg['option']['value']);
		$linklevel = $linklevel[$cfg['option']['linkage']] + 1;
		$str = '<input type="hidden" name="data['.$name.']" id="dr_'.$name.'" value="'.(int)$value.'">';
		if(!defined('FINECMS_LINKAGE_INIT_LD')) {
			define('FINECMS_LINKAGE_INIT_LD', 1);
			$str.= '<script type="text/javascript" src="'.THEME_PATH.'js/jquery.ld.js"></script>';
		}
		$level = 1;
		$default = '';
		if ($value) {
			$pids = substr($linkage[$linkageid[$value]]['pids'], 2);
			$level = substr_count($pids, ',') + 1;
			$default = !$pids ? '["'.$value.'"]' : '["'.str_replace(',', '","', $pids).'","'.$value.'"]';
		}
		// 禁止修改
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : ''; 
		// 输出默认菜单
		$str.= '<span id="dr_linkage_'.$name.'_select" style="'.($value ? 'display:none' : '').'">';
		for ($i = 1; $i <= $linklevel; $i++) {
			$style = $i > $level ? 'style="display:none"' : '';
			$str.= '<label style="padding-right:10px;"><select class="form-control finecms-select-'.$name.'" '.$disabled.' name="'.$name.'-'.$i.'" id="'.$name.'-'.$i.'" width="100" '.$style.'><option value=""> -- </option></select></label>';
		}
		$str.= '</span>';
		// 重新选择
		if ($value && !$disabled) {
			//IS_ADMIN ? $str : '<div class="form-control-static">'.$str.'</div>'
			if (IS_ADMIN) {
				$str.= '<span class="form-control-static" id="dr_linkage_'.$name.'_cxselect">'.dr_linkagepos($cfg['option']['linkage'], $value, ' » ').'&nbsp;&nbsp;<a href="javascript:;" onclick="dr_linkage_select_'.$name.'()" style="color:blue">'.fc_lang('[重新选择]').'</a></span>';
			} else {
				$str.= '<div class="form-control-static" id="dr_linkage_'.$name.'_cxselect">'.dr_linkagepos($cfg['option']['linkage'], $value, ' » ').'&nbsp;&nbsp;<a href="javascript:;" onclick="dr_linkage_select_'.$name.'()" style="color:blue">'.fc_lang('[重新选择]').'</a></div>';
			}
		}
		$str.= '
		<script type="text/javascript">
			function dr_linkage_select_'.$name.'() {
				$("#dr_linkage_'.$name.'_select").show();
				$("#dr_linkage_'.$name.'_cxselect").hide();
			}
			$(function(){
				var $ld5 = $(".finecms-select-'.$name.'");					  
				$ld5.ld({ajaxOptions:{"url": "/index.php?s=member&c=api&m=linkage&code='.$cfg['option']['linkage'].'"},defaultParentId:0})
				var ld5_api = $ld5.ld("api");
				ld5_api.selected('.$default.');
				$ld5.bind("change",onchange);
				function onchange(e){
					var $target = $(e.target);
					var index = $ld5.index($target);
					//$("#'.$name.'-'.$i.'").remove();
					$("#dr_'.$name.'").val($ld5.eq(index).show().val());
					index ++;
					$ld5.eq(index).show();
				}
			})
		</script>'.$tips;
		return $this->input_format($name, $text, $str);
	}
}