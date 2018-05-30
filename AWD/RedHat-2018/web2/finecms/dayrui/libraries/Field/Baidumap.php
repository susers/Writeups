<?php

/* v3.1.0  */

class F_Baidumap extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('百度地图'); // 字段名称
		$this->fieldtype = array('INT' => 10); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'INT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
	
		$option['city'] = isset($option['city']) ? $option['city'] : '';
		$option['level'] = isset($option['level']) ? $option['level'] : 15;
		$option['width'] = isset($option['width']) ? $option['width'] : 700;
		$option['height'] = isset($option['height']) ? $option['height'] : 430;
		
		return '<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
                    <div class="col-md-9">
                    	<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
						<label>px</label>
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
                    <label class="col-md-2 control-label">'.fc_lang('显示级层').'：</label>
                    <div class="col-md-9">
						<label><input type="text" class="form-control" size="10" name="data[setting][option][level]" value="'.$option['level'].'"></label>
						<span class="help-block">'.fc_lang('值越大地图显示越详细').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('设置城市').'：</label>
                    <div class="col-md-9">
					   <label> <input type="text" class="form-control" size="20" name="data[setting][option][city]" value="'.$option['city'].'"></label>
						<span class="help-block">'.fc_lang('多个城市以“,”分开，也可以设置当前的地区联动字段作为城市，格式为：{字段名称}').'</span>
                    </div>
                </div>';
	}
	
	/**
	 * 创建sql语句
	 */
	public function create_sql($name, $option) {
		$sql = 'ALTER TABLE `{tablename}` ADD `'.$name.'_lng` DECIMAL(9,6) NULL , ADD `'.$name.'_lat` DECIMAL(9,6) NULL';
		return $sql;
	}
	
	/**
	 * 修改sql语句
	 */
	public function alter_sql($name, $option) {
		return NULL;
	}
	
	/**
	 * 删除sql语句
	 */
	public function drop_sql($name) {
		$sql = 'ALTER TABLE `{tablename}` DROP `'.$name.'_lng`, DROP `'.$name.'_lat`';
		return $sql;
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
		
		if ($this->ci->post[$field['fieldname']]) {
			$map = explode(',', $this->ci->post[$field['fieldname']]);
			$this->ci->data[$field['ismain']][$field['fieldname'].'_lng'] = (double)$map[0];
			$this->ci->data[$field['ismain']][$field['fieldname'].'_lat'] = (double)$map[1];
		} else {
			$this->ci->data[$field['ismain']][$field['fieldname'].'_lng'] = 0;
			$this->ci->data[$field['ismain']][$field['fieldname'].'_lat'] = 0;
		}
		
	}
	
	/**
	 * 字段值
	 */
	public function get_value($name, $data) {
		return $data[$name.'_lng'] > 0 || $data[$name.'_lat'] > 0 ? $data[$name.'_lng'].','.$data[$name.'_lat'] : '';
	}
	
	/**
	 * 字段输出
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function output($value) {
		
	}
	
	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	array	$data	值
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
		// 宽度设置
		$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : 700;
		// 高度设置
		$height = isset($cfg['option']['height']) && $cfg['option']['height'] ? $cfg['option']['height'] : 430;
		// 城市设置
		$city = isset($cfg['option']['city']) && $cfg['option']['city'] ? $cfg['option']['city'] : '';
		// 显示范围
		$level = isset($cfg['option']['level']) && $cfg['option']['level'] ? $cfg['option']['level'] : 15;
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 地图默认值
		$value && list($lng, $lat) = explode(',', $value);
		$value = ($value == '0,0' || $value == '0.000000,0.000000' || strlen($value) < 5) ? '' : $value;
        // 默认城市
        $city_value = 'var city = "'.urlencode($city).'";';
        if (strpos($city, '{') !== false && strpos($city, '}') !== false) {
            $city_value = 'var city = $(".finecms-select-'.str_replace(array('{', '}'), '', $city).'").find("option:selected").text();';
        }
		// 字段默认值传递到本站api
		$str = '<script type="text/javascript">
				function map_'.$name.'_mark() {
				    '.$city_value.'
					art.dialog.open("/index.php?s=member&c=api&m=baidumap&&width='.$width.'&height='.$height.'&name='.$name.'&level='.$level.'&value='.$value.'&city="+city, {
						title: "BaiduMap",
						opacity: 0.1,
						width:'.$width.',
						height:'.$height.',
						ok: function () {
						var iframe = this.iframe.contentWindow;
						if (!iframe.document.body) {
							alert("iframe loading")
							return false;
						};
						var value = iframe.document.getElementById("'.$name.'").value,
							old = "'.$value.'";
							if (value == "") {
								$("#result_'.$name.'").html("<font color=green> '.fc_lang('标注成功').'（"+value+"）</font>");
							} else if (value != old) {
								$("#result_'.$name.'").html("<font color=blue> '.fc_lang('标注成功').'（"+value+"）</font>");
							} else {
								$("#result_'.$name.'").html("<font color=red> '.fc_lang('尚未标注').'（"+value+"）</font>");
								return true;
							}
							$("#dr_'.$name.'").val(value);
							return true;
						},
						cancel: true
					});
				}
				</script>
				<button type="button" name="'.$name.'_mark" onclick="map_'.$name.'_mark()" id="'.$name.'_mark" class="btn blue btn-sm"> <i class="fa fa-tint"></i> '.fc_lang('标注位置').'</button>
				<input name="data['.$name.']" id="dr_'.$name.'" type="hidden" value="'.$value.'" />
				<span id="result_'.$name.'">'.($value ? '<font color=green> '.fc_lang('标注成功').'（'.$value.'）</font>' : '').'</span>'.$tips;
				
		return $this->input_format($name, $text, $str);
	}
	
}