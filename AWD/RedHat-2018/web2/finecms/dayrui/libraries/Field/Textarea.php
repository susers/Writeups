<?php



class F_Textarea extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('多行文本'); // 字段名称
		$this->fieldtype = array(
			'TEXT' => ''
		); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'TEXT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
		$option['width'] = isset($option['width']) ? $option['width'] : 300;
		$option['height'] = isset($option['height']) ? $option['height'] : 100;
		$option['value'] = isset($option['value']) ? $option['value'] : '';
		$option['fieldtype'] = isset($option['fieldtype']) ? $option['fieldtype'] : '';
		$option['is_mb_auto'] = isset($option['is_mb_auto']) ? $option['is_mb_auto'] : '';
		$option['fieldlength'] = isset($option['fieldlength']) ? $option['fieldlength'] : '';
		return '
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
				</div>
			</div>
            <div class="form-group">
                <label class="col-md-2 control-label">'.fc_lang('移动端自动宽度').'：</label>
                <div class="col-md-9">
					<div class="radio-list">
						<label class="radio-inline"><input type="radio" value="0" name="data[setting][option][is_mb_auto]" '.(!$option['is_mb_auto'] ? 'checked' : '').'> '.fc_lang('是').'</label>
						<label class="radio-inline"><input type="radio" value="1" name="data[setting][option][is_mb_auto]" '.($option['is_mb_auto'] ? 'checked' : '').'> '.fc_lang('否').'</label>
					</div>
                </div>
            </div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('高度').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="10" name="data[setting][option][height]" value="'.$option['height'].'"></label>
					<label>px</label>
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
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	array	$value	值
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
		// 表单宽度设置
		if (IS_MOBILE && empty($cfg['option']['is_mb_auto'])) {
			$width = '100%';
		} else {
			$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '300';
		}
		// 表单高度设置
		$height = isset($cfg['option']['height']) && $cfg['option']['height'] ? $cfg['option']['height'] : '100';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 字段默认值
		$value = strlen($value) ? $value : $this->get_default_value($cfg['option']['value']);// 禁止修改
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : ''; 
		$str = '<textarea class="form-control" '.$disabled.' style="height:'.$height.'px; width:'.$width.(is_numeric($width) ? 'px' : '').';" name="data['.$name.']" id="dr_'.$name.'" '.$attr.'>'.$value.'</textarea>'.$tips;
		return $this->input_format($name, $text, $str);
	}
	
}