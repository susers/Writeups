<?php

/* v3.1.0  */

class F_Color extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('颜色选取') : ''; // 字段名称
		$this->fieldtype = array(
			'VARCHAR' => 10
		); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'VARCHAR'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
		$option['value'] = isset($option['value']) ? $option['value'] : '';
		$option['field'] = isset($option['field']) ? $option['field'] : 0;
		return '

			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('附加到指定字段').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="10" name="data[setting][option][field]" value="'.$option['field'].'"></label>
					<span class="help-block">'.fc_lang('对文本类型字段有效,会实时变动颜色').'</span>
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
				';
	}
	
	/**
	 * 创建sql语句
	 */
	public function create_sql($name, $option) {
		$sql = 'ALTER TABLE `{tablename}` ADD `'.$name.'` VARCHAR( 10 ) DEFAULT NULL';
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
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 字段默认值
		$value = $value ? $value : $this->get_default_value($cfg['option']['value']);
		$js = '';
		if (isset($cfg['option']['field']) && $cfg['option']['field']) {
			$js = '$("#dr_'.$cfg['option']['field'].'").css("color", $("#dr_'.$name.'").val());';
		}
		$str = '
		<label><input type="text" class="form-control color" data-control="brightness" name="data['.$name.']" id="dr_'.$name.'" value="'.$value.'" ></label>';
		$str.= '
		<script type="text/javascript">
		$(function(){
			$("#dr_'.$name.'").minicolors({
                control: $("#dr_'.$name.'").attr("data-control") || "hue",
                defaultValue: $("#dr_'.$name.'").attr("data-defaultValue") || "",
                inline: "true" === $("#dr_'.$name.'").attr("data-inline"),
                letterCase: $("#dr_'.$name.'").attr("data-letterCase") || "lowercase",
                opacity: $("#dr_'.$name.'").attr("data-opacity"),
                position: $("#dr_'.$name.'").attr("data-position") || "bottom left",
                change: function(t, o) {
                    t && (o && (t += ", " + o), "object" == typeof console && console.log(t));
                    '.$js.'
                },
                theme: "bootstrap"
            });
            '.$js.'
		});
		</script>';
        $str.= $tips;
		return $this->input_format($name, $text,  $str);
	}
	
}