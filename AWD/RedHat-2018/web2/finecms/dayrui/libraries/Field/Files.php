<?php



class F_Files extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('多文件') : ''; // 字段名称
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
	
		$data = $this->ci->get_cache('downservers');
		$downservers = '';
		
		if ($data) {
			$server = isset($option['server']) ? $option['server'] : array();
			foreach ($data as $t) {
				$downservers.= '<input '.(@in_array($t['id'], $server) ? 'checked' : '').' type="checkbox" value="'.$t['id'].'" name="data[setting][option][server][]">&nbsp;<label>'.$t['name'].'</label>&nbsp;&nbsp;&nbsp;';
			}
		}
	
		$option['count'] = isset($option['count']) ? $option['count'] : 2;
		$option['width'] = isset($option['width']) ? $option['width'] : '90%';
		$option['fieldtype'] = isset($option['fieldtype']) ? $option['fieldtype'] : '';
		$option['uploadpath'] = isset($option['uploadpath']) ? $option['uploadpath'] : '';
		$option['fieldlength'] = isset($option['fieldlength']) ? $option['fieldlength'] : '';
		$option['is_swfupload'] = isset($option['is_swfupload']) ? $option['is_swfupload'] : 0;

		return '
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
				</div>
			</div>
			<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('文件大小').'：</label>
                    <div class="col-md-9">
						<label><input id="field_default_value" type="text" class="form-control" value="'.$option['size'].'" name="data[setting][option][size]"></label>
						<span class="help-block">'.fc_lang('单位MB').'</span>
                    </div>
                </div>
            <div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('上传数量').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" value="'.$option['count'].'" name="data[setting][option][count]"></label>
					<span class="help-block">'.fc_lang('每次最多上传的文件数量').'</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('扩展名').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="40" name="data[setting][option][ext]" value="'.$option['ext'].'"></label>
					<span class="help-block">'.fc_lang('格式：jpg,gif,png,exe,html,php,rar,zip').'</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('前端SWF上传').'：</label>
				<div class="col-md-9">
					<input type="checkbox" name="data[setting][option][is_swfupload]" '.($option['is_swfupload'] ? 'checked' : '').' value="1"  data-on-text="'.fc_lang('开启').'" data-off-text="'.fc_lang('关闭').'" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
				</div>
			</div>
			';
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
	
		$data = array();
		$value = dr_string2array($value);
		if (!$value) {
            return $data;
        } elseif (!isset($value['file'])) {
            return $value;
        }
		
		foreach ($value['file'] as $i => $file) {
			$data[] = array(
				'file' => $file, // 对应文件或附件id
				'title' => $value['title'][$i] // 对应标题描述
			);
		}
		
		return $data;
	}
	
	/**
	 * 获取附件id
	 */
	public function get_attach_id($value) {


		$data = array();
        $value = dr_string2array($value);

		if (!$value) {
            return $data;
        } elseif (!isset($value['file'])) {
            return $value;
        }
		
		foreach ($value['file'] as $i => $file) {
			is_numeric($file) && $data[] = $file;
		}
		
		return $data;
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
		$data = $this->ci->post[$field['fieldname']];
		if (isset($data['pan'])) {
			unset($data['pan']);
		}
		// 第一张作为缩略图
		if (isset($_POST['data']['thumb']) && !$_POST['data']['thumb']
            && isset($data['file'][0]) && $data['file'][0]) {
            $info = get_attachment($data['file'][0]);
            if (in_array($info['fileext'], array('jpg', 'png', 'gif'))) {
			    $this->ci->data[1]['thumb'] = $data['file'][0];
            }
            unset($info);
		}
		$this->ci->data[$field['ismain']][$field['fieldname']] = dr_array2string($data);
	}
	
	/**
	 * 附件处理
	 */
	public function attach($data, $_data) {
		
		$data = dr_string2array($data);
		$_data = dr_string2array($_data);

        if (!isset($_data['file'])) {
            $_data = array('file' => NULL);
        }
		if (!isset($data['file'])) {
            $data = array('file' => NULL);
        }

		// 新旧数据都无附件就跳出
		if (!$data['file'] && !$_data['file']) {
			return NULL;
		}
		
		// 新旧数据都一样时表示没做改变就跳出
		if ($data['file'] === $_data['file']) {
			return NULL;
		}
		
		// 当无新数据且有旧数据表示删除旧附件
		if (!$data['file'] && $_data['file']) {
			return array(
				array(),
				$_data['file']
			);
		}
		
		// 当无旧数据且有新数据表示增加新附件
		if ($data['file'] && !$_data['file']) {
			return array(
				$data['file'],
				array()
			);
		}
		
		// 剩下的情况就是删除旧文件增加新文件
		
		// 新旧附件的交集，表示固定的
		$intersect = @array_intersect($data['file'], $_data['file']);
		
		return array(
			@array_diff($data['file'], $intersect), // 固有的与新文件中的差集表示新增的附件
			@array_diff($_data['file'], $intersect), // 固有的与旧文件中的差集表示待删除的附件
		);
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
		// 显示框宽度设置
		$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '100%';
		// 表单附加参数
		//$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 禁止修改
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : '';
		// 上传的URL
		$param = '&name='.$name.'&siteid='.SITE_ID.'&code='.str_replace('=', '', dr_authcode($cfg['option']['size'].'|'.$cfg['option']['ext'].'|'.$this->get_upload_path($cfg['option']['uploadpath']), 'ENCODE'));

		// 开始上传
		//(IS_ADMIN && IS_PC) || (isset($cfg['option']['is_swfupload']) && $cfg['option']['is_swfupload'])

			//pc端采用Flash上传
			$url = '/index.php?s=member&c=api&m=upload'.$param;
			$file_value = '';
			$value && $value = dr_string2array($value); // 字段默认值
			// 默认值输出
			if ($value && isset($value['file'])) {
				foreach ($value['file'] as $id => $fileid) {
					$title = $value['title'][$id];
					if (is_numeric($fileid)) {
						$edit = '<label><a href="javascript:;" class="btn btn-sm green" title="'.fc_lang('修改').'" onclick="dr_edit_file(\''.$url.'&count=1\',\''.$name.'\',\'999'.$id.'\')"><i class="fa fa-edit"></i></a></label>';
					} else {
						$edit = '<label><a href="javascript:;" class="btn btn-sm green" title="'.fc_lang('修改').'" onclick="dr_edit_input_file(\''.$fileid.'\',\''.$title.'\',\''.$name.'\',\'999'.$id.'\')"><i class="fa fa-edit"></i></a></label>';
					}
					$file_value.= '
					<li id="files_'.$name.'_999'.$id.'" list="999'.$id.'" style="cursor:move;">
						<input type="hidden" value="'.$fileid.'" name="data['.$name.'][file][]" id="fileid_'.$name.'_999'.$id.'" />
						<label><input type="text" class="form-control" style="width:300px;height:30px;" value="'.$title.'" name="data['.$name.'][title][]" /></label>
						<label><a href="javascript:;" class="btn btn-sm red" title="'.fc_lang('删除').'" onclick="dr_remove_file(\''.$name.'\',\'999'.$id.'\')"><i class="fa fa-trash"></i></a></label>
						'.$edit.'
						<label id="span_'.$name.'_999'.$id.'"><a href="javascript:;" class="btn btn-sm blue" onclick="dr_show_file_info(\''.$fileid.'\')"><i class="fa fa-search"></i></a></label>
					</li>';
				}
			}
			// 输出变量
			$str ='';
			// 加载js
			if (!defined('FINECMS_FILES_LD')) {
				$str.= '<script type="text/javascript" src="'.THEME_PATH.'js/jquery-ui.min.js"></script>';
				$str.= '<script type="text/javascript">var homeurl = "'.THEME_PATH.'"</script>';
				$str.= '<script type="text/javascript" src="'.THEME_PATH.'js/dmuploader.min.js"></script>
				<style>
				div.uploader {
					width:auto!important;
					cursor: pointer;
				}
				.uploader input {
					position: absolute;
					top: 0;
					right: 0;
					margin: 0;
					border: solid transparent;
					border-width: 0 0 100px 200px;
					opacity: .0;
					filter: alpha(opacity= 0);
					-o-transform: translate(250px,-50px) scale(1);
					-moz-transform: translate(-300px,0) scale(4);
					direction: ltr;
					cursor: pointer;
				}</style>';
				define('FINECMS_FILES_LD', 1);//防止重复加载JS
			}
			if (!$disabled) {
				$str.= '<a class="btn blue btn-sm" href="javascript:;" onClick="dr_upload_files(\'' . $name . '\',\'' . $url . '\', \'\', \'' . (int)$cfg['option']['count'] . '\')"> <i class="fa fa-upload"></i> ' . fc_lang('上传文件') . '</a>&nbsp;&nbsp;';
				$str.= '<a class="btn blue btn-sm" href="javascript:;" onClick="dr_input_files(\'' . $name . '\', \'' . (int)$cfg['option']['count'] . '\')"> <i class="fa fa-plus"></i> ' . fc_lang('输入地址') . '</a>';
				$str.= '<div id="drag-and-drop-zone-'.$name.'" class="btn btn-sm blue uploader" style="cursor: pointer;margin-left:10px;"><i class="fa fa-cloud-upload"></i> '.fc_lang('快速上传').' <input type="file" name="file"></div>';
				$str.= '
				<script type="text/javascript">
				  var dr_'.$name.'_ids = new Array;
				  $("#drag-and-drop-zone-'.$name.'").dmUploader({
					url: "/index.php?s=member&c=api&m=new_ajax_upload'.$param.'",
					dataType: "json",
					allowedTypes: "*",
					onInit: function(){
					  //alert("Plugin initialized correctly");
					},
					onBeforeUpload: function(id){
					  var vhtml = "";
					  var size = $("#'.$name.'-sort-items li").size();
					  var mid = parseInt(size)+parseInt(id);
					  dr_'.$name.'_ids[id] = mid;
					  
					  vhtml+= "<li id=\"files_'.$name.'_"+mid+"\" list=\""+mid+"\">";
					  vhtml+= "<div id=\"loading_'.$name.'_"+mid+"\" style=\"padding-top:10px\">";
					  vhtml+= "<div class=\"progress progress-striped\">";
					  vhtml+= "<div class=\"progress-bar progress-bar-warning\" role=\"progressbar\" style=\"width: 10%\">";
					  vhtml+= "<span class=\"sr-only\"></span>";
					  vhtml+= "</div>";
					  vhtml+= "</div>";
					  vhtml+= "</div>";
					  vhtml+= "</li>";
					  
					  $("#'.$name.'-sort-items").append(vhtml);
					},
					onNewFile: function(id, file){
					  //alert(file);
					},
					onComplete: function(){
					  //alert("All pending tranfers completed");
					},
					onUploadProgress: function(id, percent){
					  var mid = dr_'.$name.'_ids[id];
					  $("#loading_'.$name.'_"+mid+" .progress-bar-warning").attr("style", "width:"+percent + "%");
					},
					onUploadSuccess: function(id, data){
					  var mid = dr_'.$name.'_ids[id];
					  if (data.code == 1) {
					  	 var name = "'.$name.'";
					 	 var c = "";
						  c += \'<input type="hidden" value="\' + data.id + \'" name="data['.$name.'][file][]" id="fileid_'.$name.'_\' + id + \'" />\';
						  c += \'<label><input type="text" class="form-control" style="width:300px;height:30px;" value="\' + data.name + \'" name="data['.$name.'][title][]" /></label>\t\';
						  c += \'<label><a href="javascript:;" class="btn btn-sm red" onclick="dr_remove_file(\\\'\' + name + \'\\\',\\\'\' + id + \'\\\')">\';
						  c += \'<i class="fa fa-trash"></i></a></label>\t\';
						  c += \'<label id="span_\' + name + \'_\' + id + \'"><a href="javascript:;" class="btn btn-sm blue" onclick="dr_show_file_info(\\\'\' + data.id + \'\\\')\">\';
						  c += \'<i class="fa fa-search"></i></a></label>\t\';
						  $("#files_'.$name.'_"+mid).html(c);
					  } else {
						$("#files_'.$name.'_"+mid).remove();
						alert(data.msg);
					  }
			
					},
					onUploadError: function(id, message){
					  alert("Failed to Upload file #" + id + ": " + message);
					  var mid = dr_'.$name.'_ids[id];
					  $("#files_'.$name.'_"+mid).html("");
					},
					onFileTypeError: function(file){
					  alert("File \"" + file.name + "\" cannot be added: must be an image");
					  var mid = dr_'.$name.'_ids[id];
					  $("#files_'.$name.'_"+mid).html("");
					},
					onFileSizeError: function(file){
					  alert( "File \"" + file.name + "\" cannot be added: size excess limit");
					  var mid = dr_'.$name.'_ids[id];
					  $("#files_'.$name.'_"+mid).html("");
					},
					onFallbackMode: function(message){
					  alert( "Browser not supported(do something else here!): " + message);
					  var mid = dr_'.$name.'_ids[id];
					  $("#files_'.$name.'_"+mid).html("");
					}
				  });
				</script>
				';
			}

			$str.= '	<div class="picList" id="list_'.$name.'_files" style="margin-top:10px">';
			$str.= '		<ul id="'.$name.'-sort-items">';
			$str.= 				$file_value;
			$str.= '		</ul>';
			$str.= '	</div>';
			$str.= '<div class="bk10"></div>';
			$str.= '<script type="text/javascript">$("#'.$name.'-sort-items").sortable();</script>'.$tips.'';

		// 输出最终表单显示
		return $this->input_format($name, $text, $str);
	}
	
}