<?php



class F_Related extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('内容关联'); // 字段名称
		$this->fieldtype = array('TEXT' => ''); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'TEXT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
	
		$_option = '';
		$_module = $this->ci->get_cache('module');
		
		if ($_module) {
			foreach ($_module as $dir => $t) {
				$_option.= '<option value="'.$dir.'" '.($dir == $option['module'] ? 'selected' : '').'>'.$t['name'].'</option>';
			}
		}
		$option['width'] = isset($option['width']) ? $option['width'] : '90%';
		
		return '<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('模型').'：</label>
                    <div class="col-md-9">
                    <label><select class="form-control" name="data[setting][option][module]">
					'.$_option.'
					</select></label>
					<span class="help-block">'.fc_lang('必须选择一个模型作为关联数据源').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
                    <div class="col-md-9">
                    <label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('最大显示数量').'：</label>
                    <div class="col-md-9">
                    <label><input type="text" class="form-control" size="10" name="data[setting][option][limit]" value="'.$option['limit'].'"></label>
					<span class="help-block">'.fc_lang('关联列表搜索结果最大显示数量，默认50条').'</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('友情提示').'：</label>
                    <div class="col-md-9">
                        <div class="form-control-static">'.fc_lang('此字段不能参与搜索条件筛选').'</div>
                    </div>
                </div>';
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
		return $value;
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
		
		$data = $this->ci->post[$field['fieldname']];
		$value = !$data ? '' : implode(',', $data);
		
		$this->ci->data[$field['ismain']][$field['fieldname']] = $value;
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
		$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '80%';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 禁止修改
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : ''; 
		// 模型名称
		$module = isset($cfg['option']['module']) ? $cfg['option']['module'] : '';
		//
		$tpl = '<li id="files_'.$name.'_{id}" style="padding-right:10px;cursor:move;border-bottom: 1px solid #EEEEEE;"><a href="javascript:;" onclick="dr_remove_file(\''.$name.'\',\'{id}\')"><img align="absmiddle" src="'.THEME_PATH.'admin/images/b_drop.png"></a>&nbsp;{value}<input type="hidden" name="data['.$name.'][]" value="{id}"></li>';
		//
		$str = '<fieldset class="blue pad-10" style="width:'.$width.(is_numeric($width) ? 'px' : '').';">';
		$str.= '<legend>'.$cname.'</legend>';
		$str.= '<div class="picList">';
		$str.= '<ul class="'.$name.'-sort-items" id="dr_list_'.$name.'" style="max-height: 400px;overflow-y: auto;">';
        $value = @trim($value, ',');
        if ($value && is_string($value)) {
			$query = $this->ci->db->query('select id,title,url from '.$this->ci->db->dbprefix(SITE_ID.'_'.$module).' where id IN ('.$value.') order by instr("'.$value.'", id)')->result_array();
			foreach ($query as $t) {
				$id = $t['id'];
				$value = '<a href="'.$t['url'].'" target="_blank">'.$t['title'].'</a>';
				$str.= str_replace(array('{id}', '{value}'), array($id, $value), $tpl);
			}
		}	
		$str.= '</ul>';
		$str.= '</div>';
		$str.= '<div class="bk10"></div>';
		if(!defined('FINECMS_LINKAGE_INIT_LD')) {
			define('FINECMS_LINKAGE_INIT_LD', 1);
			$str.= '<script type="text/javascript" src="'.THEME_PATH.'js/jquery.ld.js"></script>';
		}
		$str.= '
		<script type="text/javascript">
		function dr_add_related_'.$name.'() {
			art.dialog.open("/index.php?s=member&c=api&m=related&site='.SITE_ID.'&module='.$module.'&limit='.intval($cfg['option']['limit']).'", {
				title: "'.$cname.'",
				opacity: 0.1,
				width: 700,
				height: 400,
				ok: function () {
					var iframe = this.iframe.contentWindow;
					if (!iframe.document.body) {
						alert("iframe loading")
						return false;
					};
					var id;
					var value;
					var err = 0;
					var select = iframe.document.getElementsByName("ids[]");
					for (var i=0; i < select.length; i++) {
						if (select[i].checked) {
							id = select[i].value;
							value = iframe.document.getElementById("dr_row_"+id).innerHTML;
							if ($("#files_'.$name.'_"+id).size() == 0) {
								var html = \''.addslashes(str_replace(array("\r", "\n", "\t", chr(13)), '', $tpl)).'\';
								html = html.replace(/{id}/g, id);
								html = html.replace(/{value}/g, value);
								$("#dr_list_'.$name.'").append(html);
							} else {
								err ++;
							}
						}
					}
					if (err > 0) {
						dr_tips("有"+err+"条记录已经存在了");
					}
				},
				cancel: true
			});
		}
		$(".'.$name.'-sort-items").sortable();
		</script>
		</fieldset>';
		$str.= '<div class="bk10"></div>';
		$str.= '<button type="button" class="btn blue btn-sm" onClick="dr_add_related_'.$name.'()"> <i class="fa fa-plus"></i> 添加</button>';
		$str.= $tips;
		
		return $this->input_format($name, $text, $str);
	}
}