<?php

/* v3.1.0  */

class F_Date extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('日期时间') : ''; // 字段名称
		$this->fieldtype = array(
			'INT' => 10
		); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'INT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
		$option['width'] = isset($option['width']) ? $option['width'] : 200;
		$option['format'] = isset($option['format']) ? $option['format'] : '';
		return '
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('显示格式').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="30" name="data[setting][option][format]" value="'.$option['format'].'"></label>
					<span class="help-block">'.fc_lang('参数与date函数一致，如果留空采用网站默认设置格式').'</span>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('默认值').'：</label>
				<div class="col-md-9">
					<label><input id="field_default_value" type="text" class="form-control" size="20" value="'.$option['value'].'" name="data[setting][option][value]"></label>
					<label>'.$this->member_field_select().'</label>
					<span class="help-block">'.fc_lang('也可以设置会员表字段，表示用当前登录会员信息来填充这个值').'</span>
				</div>
			</div>
			'.$this->field_type($option['fieldtype'], $option['fieldlength']);
	}
	
	/**
	 * 创建sql语句
	 */
	public function create_sql($name, $option) {
		// 无符号int 10位
		$sql = 'ALTER TABLE `{tablename}` ADD `'.$name.'` INT( 10 ) UNSIGNED NULL';
		return $sql;
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
		return dr_date($value, NULL, 'red');
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
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 字段默认值
		if (is_null($value)) {
			$value = $cfg['option']['value'] === '0' ? 0 : SYS_TIME;
		} else {
			$value = $value ? $value : (strlen($value) == 1 && $value == 0 ? '' : SYS_TIME);
		}
		$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '200';
		$format = isset($cfg['option']['format']) && $cfg['option']['format'] ? $cfg['option']['format'] : SITE_TIME_FORMAT;
		$format = str_replace(array('i', 's'), array('M', 'S'), $format); //%Y-%m-%d %H:%M:%S
		$format = @preg_replace('/([a-z]+)/i', '%$1', $format);
		$show = $value ? date(str_replace(array('%','M','S'), array('','i','s'), $format), $value) : '';
		
		$str = '';
		if (!defined('DAYRUI_DATE_LD')) {
			$str.= '
			<link rel="stylesheet" type="text/css" href="'.THEME_PATH.'js/calendar2/css/jscal2.css"/>
			<link rel="stylesheet" type="text/css" href="'.THEME_PATH.'js/calendar2/css/border-radius.css"/>
			<script type="text/javascript" src="'.THEME_PATH.'js/calendar2/jscal2.js"></script>
			<script type="text/javascript" src="'.THEME_PATH.'js/calendar2/lang/'.SITE_LANGUAGE.'.js"></script>';
			define('DAYRUI_DATE_LD', 1);//防止重复加载JS
		}
		$vname = str_replace(array('[', ']'), '-', $name);
		$str.= '
		<input type="hidden" value="'.$value.'" name="data['.$name.']" id="dr_'.$vname.'" '.$attr.' />
		<label><input ondblclick="dr_clear_date(\''.$vname.'\')" type="text" readonly="" style="width:'.$width.(is_numeric($width) ? 'px' : '').';" class="mydate form-control" value="'.$show.'" id="calendar_'.$vname.'" /></label>
		<script type="text/javascript">
			Calendar.setup({
			weekNumbers : true,
			inputField  : "calendar_' . $vname . '",
			trigger     : "calendar_' . $vname . '",
			dateFormat  : "' . $format . '",
			showTime    : true,
			minuteStep  : 1,
			onSelect    : function() {
				this.hide();
				var time = $("#calendar_' . $vname . '").val();
				var date = (new Date(Date.parse(time.replace(/-/g,"/")))).getTime() / 1000;
				$("#dr_' . $vname . '").val(date);
			}
			});
		</script>';
        if (APP_DIR && $name == 'updatetime') {
            $str.= '<label><input name="no_time" type="checkbox" value="1" /> '.fc_lang('不更新').'</label>';
        }
		$str.= $tips;

		return $this->input_format($name, $text, '<span class="form-date input-group">'.$str.'</span>');
	}
}