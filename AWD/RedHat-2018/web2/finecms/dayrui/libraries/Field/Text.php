<?php



class F_Text extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('单行文本') : ''; // 字段名称
		$this->fieldtype = TRUE; // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'VARCHAR'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
        $unique = '';
        $option['ispwd'] = isset($option['ispwd']) ? $option['ispwd'] : 0;
        $option['value'] = isset($option['value']) ? $option['value'] : '';
		$option['width'] = isset($option['width']) ? $option['width'] : 200;
        $option['unique'] = isset($option['unique']) ? $option['unique'] : 0;
		$option['fieldtype'] = isset($option['fieldtype']) ? $option['fieldtype'] : '';
        $option['is_mb_auto'] = isset($option['is_mb_auto']) ? $option['is_mb_auto'] : '';
		$option['fieldlength'] = isset($option['fieldlength']) ? $option['fieldlength'] : '';
        if (TEXT_UNIQUE) {
            $unique.= '
            <div class="form-group">
                <label class="col-md-2 control-label">'.fc_lang('验证重复').'：</label>
                <div class="col-md-9">
                	<input type="checkbox" name="data[setting][option][unique]" '.($option['unique'] ? 'checked' : '').' value="1"  data-on-text="'.fc_lang('开启').'" data-off-text="'.fc_lang('关闭').'" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
					<span class="help-block">'.fc_lang('开启将会判断此字段的唯一性（只对主表有效）').'</span>
                </div>
            </div>
            ';
        }
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
                <label class="col-md-2 control-label">'.fc_lang('密码框模式').'：</label>
                <div class="col-md-9">
                	<input type="checkbox" name="data[setting][option][ispwd]" '.($option['ispwd'] ? 'checked' : '').' value="1"  data-on-text="'.fc_lang('开启').'" data-off-text="'.fc_lang('关闭').'" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">

					<span class="help-block">'.fc_lang('开启之后它将作为密码框来显示').'</span>
                </div>
            </div>
			'.$unique.'
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
     * 字段入库值
     *
     * @param	array	$field	字段信息
     * @return  void
     */
    public function insert_value($field) {
		// 格式化入库值
		$value = $this->ci->post[$field['fieldname']];
		if (in_array($field['setting']['option']['fieldtype'], array('INT', 'TINYINT', 'SMALLINT'))) {
			$this->ci->data[$field['ismain']][$field['fieldname']] = $value ? (int)$value : 0;
		} elseif (in_array($field['setting']['option']['fieldtype'], array('DECIMAL', 'FLOAT'))) {
			$this->ci->data[$field['ismain']][$field['fieldname']] = $value ? (float)$value : 0;
		} elseif ($field['setting']['option']['fieldtype'] == 'MEDIUMINT') {
			$this->ci->data[$field['ismain']][$field['fieldname']] = $value ? $value : 0;
		} else {
			$this->ci->data[$field['ismain']][$field['fieldname']] = htmlspecialchars($value);
		}
    }

	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	array	$value	值
	 * @param	array	$id		当前内容表的id（表示非发布操作）
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
        // 是否密码框
        $type = isset($cfg['option']['ispwd']) && $cfg['option']['ispwd'] ? 'password' : 'text';
		// 表单宽度设置
        if (IS_MOBILE && empty($cfg['option']['is_mb_auto'])) {
            $width = '100%';
        } else {
            $width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '200';
        }
		$style = 'style="width:'.$width.(is_numeric($width) ? 'px' : '').';"';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = ($name == 'title' && APP_DIR) || (isset($cfg['validate']['tips']) && $cfg['validate']['tips']) ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 字段默认值
		$value = (@strlen($value) ? $value : $this->get_default_value($cfg['option']['value']));
		// 禁止修改
        if (!IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit']) {
            $str = '<input type="hidden" name="data['.$name.']" id="dr_'.$name.'" value="'.$value.'"> <div class="form-control-static">'.$value.'</div>'.($cfg['append'] ? $cfg['append'] : '');
        } else {
            // 当字段必填时，加入html5验证标签
            $required = isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? ' required="required"' : '';
            if (in_array($name, array('order_quantity', 'order_volume'))) {
                $required = '';
            }
			$str = '<input class="form-control" type="'.$type.'" name="data['.$name.']" id="dr_'.$name.'" value="'.$value.'" '.$style.$required.' '.$attr.' />';
			if ($cfg['append']) {
				$str = '<label>'.$str.'</label>'.$cfg['append'];
			}
        }
		return $this->input_format($name, $text, $str.$tips);
	}
	
}