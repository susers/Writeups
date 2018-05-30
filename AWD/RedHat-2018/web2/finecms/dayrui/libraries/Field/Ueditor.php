<?php



class F_Ueditor extends A_Field {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->name = 'Ueditor';	// 字段名称
        $this->fieldtype = array('MEDIUMTEXT' => ''); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
        $this->defaulttype = 'MEDIUMTEXT'; // 当用户没有选择字段类型时的缺省值
    }

    /**
     * 字段相关属性参数
     *
     * @param	array	$value	值
     * @return  string
     */
    public function option($option) {

        $option['key'] = isset($option['key']) ? $option['key'] : '';
        $option['mode'] = isset($option['mode']) ? $option['mode'] : 1;
        $option['page'] = isset($option['page']) ? $option['page'] : 0;
        $option['tool'] = isset($option['tool']) ? $option['tool'] : '\'bold\', \'italic\', \'underline\'';
        $option['mode2'] = isset($option['mode2']) ? $option['mode2'] : $option['mode'];
        $option['tool2'] = isset($option['tool2']) ? $option['tool2'] : $option['tool'];
        $option['mode3'] = isset($option['mode3']) ? $option['mode3'] : $option['mode'];
        $option['tool3'] = isset($option['tool3']) ? $option['tool3'] : $option['tool'];
        $option['value'] = isset($option['value']) ? $option['value'] : '';
        $option['width'] = isset($option['width']) ? $option['width'] : '100%';
        $option['height'] = isset($option['height']) ? $option['height'] : 200;
        $option['divtop'] = isset($option['divtop']) ? $option['divtop'] : 0;
        $option['fieldtype'] = isset($option['fieldtype']) ? $option['fieldtype'] : '';
        $option['autofloat'] = isset($option['autoheight']) ? $option['autoheight'] : 0;
        $option['autoheight'] = isset($option['autoheight']) ? $option['autoheight'] : 0;
        $option['fieldlength'] = isset($option['fieldlength']) ? $option['fieldlength'] : '';

        return '<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
                    <div class="col-md-9">
                        <label><input type="text" class="form-control" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					    <span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('高度').'：</label>
                    <div class="col-md-9">
                        <label><input type="text" class="form-control" name="data[setting][option][height]" value="'.$option['height'].'"></label>
					    <label>px</label>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('参数配置').'：</label>
                    <div class="col-md-9">
                    <fieldset class="blue pad-10">
						<legend>'.fc_lang('参数配置').'</legend>
						<div class="form-group">
							<div class="form-group">
								<label class="col-md-3 control-label">'.fc_lang('固定工具栏').'：</label>
								<div class="col-md-9">
                                <div class="radio-list">
								    <label class="radio-inline"><input type="radio" value="1" name="data[setting][option][autofloat]" '.($option['autofloat'] == 1 ? 'checked' : '').' > '.fc_lang('开启').'</label>
								    <label class="radio-inline"><input type="radio" value="0" name="data[setting][option][autofloat]" '.($option['autofloat'] == 0 ? 'checked' : '').' > '.fc_lang('关闭').'</label>
								</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">'.fc_lang('自动伸长高度').'：</label>
								<div class="col-md-9">
                                <div class="radio-list">
								    <label class="radio-inline"><input type="radio" value="1" name="data[setting][option][autoheight]" '.($option['autoheight'] == 1 ? 'checked' : '').' > '.fc_lang('开启').'</label>
								    <label class="radio-inline"><input type="radio" value="0" name="data[setting][option][autoheight]" '.($option['autoheight'] == 0 ? 'checked' : '').' > '.fc_lang('关闭').'</label>
								</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">'.fc_lang('远程图片模式').'：</label>
								<div class="col-md-9">
                                <div class="radio-list">
								    <label class="radio-inline"><input type="radio" value="1" name="data[setting][option][autodown]" '.($option['autodown'] == 1 ? 'checked' : '').' > '.fc_lang('队列（高效）').'</label>
								    <label class="radio-inline"><input type="radio" value="2" name="data[setting][option][autodown]" '.($option['autodown'] == 2 ? 'checked' : '').' > '.fc_lang('自动（会影响发布速度）').'</label>
								    <label class="radio-inline"><input type="radio" value="0" name="data[setting][option][autodown]" '.($option['autodown'] == 0 ? 'checked' : '').' > '.fc_lang('关闭').'</label>
								</div>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-3 control-label">'.fc_lang('分页标签').'：</label>
								<div class="col-md-9">
                                <div class="radio-list">
								    <label class="radio-inline"><input type="radio" value="1" name="data[setting][option][page]" '.($option['page'] ? 'checked' : '').' > '.fc_lang('开启').'</label>
								    <label class="radio-inline"><input type="radio" value="0" name="data[setting][option][page]" '.(!$option['page'] ? 'checked' : '').' > '.fc_lang('关闭').'</label>
								</div>
								</div>
							</div>
						</div>
					</fieldset>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('后台编辑器模式').'：</label>
                    <div class="col-md-9">
                        <div class="radio-list">
                            <label class="radio-inline"><input type="radio" value="1" name="data[setting][option][mode]" '.($option['mode'] == 1 ? 'checked' : '').' onclick="$(\'#bjqms1\').hide()"> '.fc_lang('完整').'</label>
                            <label class="radio-inline"><input type="radio" value="2" name="data[setting][option][mode]" '.($option['mode'] == 2 ? 'checked' : '').' onclick="$(\'#bjqms1\').hide()"> '.fc_lang('精简').'</label>
                            <label class="radio-inline"><input type="radio" value="3" name="data[setting][option][mode]" '.($option['mode'] == 3 ? 'checked' : '').' onclick="$(\'#bjqms1\').show()"> '.fc_lang('自定义').'</label>
                        </div>
                    </div>
                </div>
				<div class="form-group" id="bjqms1" '.($option['mode'] < 3 ? 'style="display:none"' : '').'>
                    <label class="col-md-2 control-label">'.fc_lang('工具栏').'：</label>
                    <div class="col-md-9">
                    <textarea name="data[setting][option][tool]" style="height:90px;" class="form-control">'.$option['tool'].'</textarea>
					<span class="help-block">'.fc_lang('必须严格按照Ueditor工具栏格式：\'fullscreen\', \'source\', \'|\', \'undo\', \'redo\'').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('前端编辑器模式').'：</label>
                    <div class="col-md-9">
                        <div class="radio-list">
                            <label class="radio-inline"><input type="radio" value="1" name="data[setting][option][mode2]" '.($option['mode2'] == 1 ? 'checked' : '').' onclick="$(\'#bjqms2\').hide()"> '.fc_lang('完整').'</label>
                            <label class="radio-inline"><input type="radio" value="2" name="data[setting][option][mode2]" '.($option['mode2'] == 2 ? 'checked' : '').' onclick="$(\'#bjqms2\').hide()"> '.fc_lang('精简').'</label>
                            <label class="radio-inline"><input type="radio" value="3" name="data[setting][option][mode2]" '.($option['mode2'] == 3 ? 'checked' : '').' onclick="$(\'#bjqms2\').show()"> '.fc_lang('自定义').'</label>
                        </div>
                    </div>
                </div>
				<div class="form-group" id="bjqms2" '.($option['mode2'] < 3 ? 'style="display:none"' : '').'>
                    <label class="col-md-2 control-label">'.fc_lang('工具栏').'：</label>
                    <div class="col-md-9">
                    <textarea name="data[setting][option][tool2]" style="height:90px;" class="form-control">'.$option['tool2'].'</textarea>
					<span class="help-block">'.fc_lang('必须严格按照Ueditor工具栏格式：\'fullscreen\', \'source\', \'|\', \'undo\', \'redo\'').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('移动端编辑器模式').'：</label>
                    <div class="col-md-9">
                        <div class="radio-list">
                            <label class="radio-inline"><input type="radio" value="1" name="data[setting][option][mode3]" '.($option['mode3'] == 1 ? 'checked' : '').' onclick="$(\'#bjqms3\').hide()"> '.fc_lang('完整').'</label>
                            <label class="radio-inline"><input type="radio" value="2" name="data[setting][option][mode3]" '.($option['mode3'] == 2 ? 'checked' : '').' onclick="$(\'#bjqms3\').hide()"> '.fc_lang('精简').'</label>
                            <label class="radio-inline"><input type="radio" value="3" name="data[setting][option][mode3]" '.($option['mode3'] == 3 ? 'checked' : '').' onclick="$(\'#bjqms3\').show()"> '.fc_lang('自定义').'</label>
                        </div>
                    </div>
                </div>
				<div class="form-group" id="bjqms3" '.($option['mode3'] < 3 ? 'style="display:none"' : '').'>
                    <label class="col-md-2 control-label">'.fc_lang('工具栏').'：</label>
                    <div class="col-md-9">
                    <textarea name="data[setting][option][tool3]" style="height:90px;" class="form-control">'.$option['tool3'].'</textarea>
					<span class="help-block">'.fc_lang('必须严格按照Ueditor工具栏格式：\'fullscreen\', \'source\', \'|\', \'undo\', \'redo\'').'</span>
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
     * 字段入库值
     */
    public function insert_value($field) {

        $down = FALSE;
        $value = $this->ci->post[$field['fieldname']];
        $value = str_replace('class="pagebreak" name="dr_page_break"', 'name="dr_page_break" class="pagebreak"', $value);
        $value = str_replace('name="dr_page_break" class="pagebreak"', 'class="pagebreak"', $value);
        $value = str_replace('id="undefined"', '', $value);
        //$value = preg_replace('/\.(gif|jpg|jpeg|png)\?(.*)(\'|")/iU', '.$1$3', $value);
        $value = preg_replace('/\.(gif|jpg|jpeg|png)@(.*)(\'|")/iU', '.$1$3', $value);
        $attach = array();

        // 下载远程图片
        if (isset($field['setting']['option']['autodown'])
            && $field['setting']['option']['autodown']
            && preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|png))\\2/i", $value, $imgs)) {
            // 当前作者
            $uid = isset($_POST['data']['uid']) ? (int)$_POST['data']['uid'] : $this->ci->uid;
            // 附件总大小判断
            if ($uid == $this->ci->uid
                && ($this->ci->member['adminid'] || $this->ci->member_rule['attachsize'])) {
                $data = $this->ci->db->select_sum('filesize')->where('uid', $uid)->get('attachment')->row_array();
                if ($this->ci->member['adminid']
                    || $data['filesize'] <= $this->ci->member_rule['attachsize'] * 1024 * 1024) {
                    $down = TRUE;
                } else {
                    // 附件总空间不足
                    $this->ci->member_model->add_notice($uid, 1, fc_lang('附件可用空间不足，无法下载远程图片'));
                }
            }
            // 处理远程图片
            $this->ci->load->model('attachment_model');
            $imgs[3] = array_unique($imgs[3]);
            $imgs[0] = array_unique($imgs[0]);
            foreach ($imgs[3] as $i => $img) {
				if (!$img
                    || strpos($img, 'http') !== 0
                    || preg_match('/id="'.UEDITOR_IMG_ID.'_img_([0-9]+)"([\w|\s|=\."]*)src="'.str_replace('/', '\/', $img).'"/iU', $value, $match)) {
                    continue;
                }
                $timg = 1;
                // 开始下载远程图片
                if ($down && $uid) {
					if ($field['setting']['option']['autodown'] == 1) {
						// 队列下载
						$id = $this->ci->attachment_model->cron_catcher($uid, $img, $field);
						if ($id) {
							$timg = 0;
							$value = str_replace($imgs[0][$i], " id=\"".UEDITOR_IMG_ID."_img_$id\" src=\"".$img."?s=finecms\"", $value);
							$attach[] = $id;
						}
					} else {
						// 当前下载
						$file = dr_catcher_data($img);
						if (!$file) {
							log_message('error', '编辑器下载远程图片失败：获取远程数据失败('.$img.')');
						} else {
							$path = SYS_UPLOAD_PATH.'/ueditor/image/'.date('Ym', SYS_TIME).'/';
                            !is_dir($path) && dr_mkdirs($path);
							$fileext = strtolower(trim(substr(strrchr($img, '.'), 1, 10))); //扩展名
							$filename = substr(md5(time()), 0, 7).rand(100, 999);
							if (@file_put_contents($path.$filename.'.'.$fileext, $file)) {
								$info = array(
									'file_ext' => '.'.$fileext,
									'full_path' => $path.$filename.'.'.$fileext,
									'file_size' => filesize($path.$filename.'.'.$fileext)/1024,
									'client_name' => $img,
								);
								$this->ci->load->model('attachment_model');
								$result = $this->ci->attachment_model->upload($uid, $info);
								if (is_array($result)) {
									$id = $result[0];
									$file = $result[1];
									$value = str_replace($imgs[0][$i], " id=\"".UEDITOR_IMG_ID."_img_$id\" src=\"".$file."\"", $value);
									$attach[] = $id;
								} else {
									@unlink($path.$filename.'.'.$fileext);
									log_message('error', '编辑器下载远程图片失败：'.$result);
								}
							} else {
								log_message('error', '编辑器下载远程图片失败：文件写入失败');
							}
						}
					}
                   
                }
                // 当不下载图片
                if ($timg) {
                    if (preg_match('/<img id="'.UEDITOR_IMG_ID.'_img_([0-9]+)"([\w|\s|=\."]*)src="'.str_replace('/', '\/', $img).'"/iU', $value, $match)) {
                        $attach[] = (int)$match[1];
                    } elseif (preg_match('/<img src="'.str_replace('/', '\/', $img).'"(.*)id="'.UEDITOR_IMG_ID.'_img_([0-9]+)"/iU', $value, $match)) {
                        $attach[] = (int)$match[2];
                    } else {
                        strpos($img, 'ueditor') === FALSE && $attach[] = $img;
                    }
                }
            }
        }

        // 第一张作为缩略图
        if ($field['fieldname'] == 'content'
            && isset($_POST['data']['thumb'])
            && !$_POST['data']['thumb']) {
            if ($attach) {
                $this->ci->data[1]['thumb'] = $attach[0];
            } elseif (preg_match("/index\.php\?c=api&m=thumb&id=([0-9]+)&/U", $value, $img)) {
                // 筛选是否存在图片
                $this->ci->data[1]['thumb'] = intval($img[1]);
            }
        }

        $this->ci->data[$field['ismain']][$field['fieldname']] = $value;
    }

    /**
     * 字段输出
     *
     * @param	array	$value	数据库值
     * @return  string
     */
    public function output($value) {
        return $value;
    }

    /**
     * 获取附件id
     */
    public function get_attach_id($value) {

        $data = array();
        $pregs = array('/id="'.UEDITOR_IMG_ID.'_img_([0-9]+)"/iU');

        foreach ($pregs as $preg) {
            if (preg_match_all($preg, $value, $aid)) {
                foreach ($aid[1] as $i => $id) {
                    $data[] = (int)$id;
                }
            }
        }

        return $data;
    }

    /**
     * 附件处理
     */
    public function attach($data, $_data) {

        $data1 = $data2 = array();
        $pregs = array('/id="'.UEDITOR_IMG_ID.'_img_([0-9]+)"/iU');

        // 新数据筛选附件
        foreach ($pregs as $preg) {
            if (preg_match_all($preg, $data, $aid)) {
                foreach ($aid[1] as $i => $id) {
                    $data1[] = (int)$id;
                }
            }
        }

        // 旧数据筛选附件
        foreach ($pregs as $preg) {
            if (preg_match_all($preg, $_data, $aid)) {
                foreach ($aid[1] as $i => $id) {
                    $data2[] = (int)$id;
                }
            }
        }

        // 新旧数据都无附件就跳出
        if (!$data1 && !$data2) {
            return NULL;
        }

        // 新旧数据都一样时表示没做改变就跳出
        if ($data1 === $data2) {
            return NULL;
        }

        // 当无新数据且有旧数据表示删除旧附件
        if (!$data1 && $data2) {
            return array(
                array(),
                $data2
            );
        }

        // 当无旧数据且有新数据表示增加新附件
        if ($data1 && !$data2) {
            return array(
                $data1,
                array()
            );
        }

        // 剩下的情况就是删除旧文件增加新文件

        // 新旧附件的交集，表示固定的
        $intersect = @array_intersect($data1, $data2);

        return array(
            array_diff($data1, $intersect), // 固有的与新文件中的差集表示新增的附件
            array_diff($data2, $intersect), // 固有的与旧文件中的差集表示待删除的附件
        );
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
        $width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '90%';

        // 表单高度设置
        $page = isset($cfg['option']['page']) && $cfg['option']['page'] ? (int)$cfg['option']['page'] : 0;
        $height = isset($cfg['option']['height']) && $cfg['option']['height'] ? $cfg['option']['height'] : '300';

        // 字段提示信息
        $tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';

        // 字段默认值
        $value = strlen($value) ? $value : $this->get_default_value($cfg['option']['value']);

        // 输出
        $str = '';
        $ueurl = '/api/ueditor/';

        // 防止重复加载JS
        if (!defined('DAYRUI_UEDITOR_LD')) {
            $str.= '
			<script type="text/javascript">var DR_DIV2P = false;</script>
			<script type="text/javascript" src="'.$ueurl.'ueditor.config.js"></script>
			<script type="text/javascript" src="'.$ueurl.'ueditor.all.min.js"></script>
			<script type="text/javascript" src="'.$ueurl.'lang/'.SITE_LANGUAGE.'/'.SITE_LANGUAGE.'.js"></script>';
            define('DAYRUI_UEDITOR_LD', 1);
        }

        $tool = IS_ADMIN ? "'fullscreen', 'source', '|', " : ''; // 后台引用时显示html工具栏
        $pagebreak = $page ? ', \'pagebreak\'' : '';

        // 编辑器模式
        $mode = 1;
        if (IS_ADMIN) {
            $mode = $cfg['option']['mode'];
        } else {
            if (IS_MOBILE) {
                $mode = isset($cfg['option']['mode3']) && $cfg['option']['mode3'] ? $cfg['option']['mode3'] : $cfg['option']['mode'];
                $cfg['option']['tool'] = isset($cfg['option']['tool3']) && $cfg['option']['tool3'] ? $cfg['option']['tool3'] : $cfg['option']['tool'];
            } else {
                $mode = isset($cfg['option']['mode2']) && $cfg['option']['mode2'] ? $cfg['option']['mode2'] : $cfg['option']['mode'];
                $cfg['option']['tool'] = isset($cfg['option']['tool2']) && $cfg['option']['tool2'] ? $cfg['option']['tool2'] : $cfg['option']['tool'];
            }

        }
        // 编辑器工具
        switch ($mode) {
            case 3: // 自定义
                $tool.= $cfg['option']['tool'];
                break;
            case 2: // 精简
                $tool.= "'undo', 'redo', '|',
						'bold', 'italic', 'underline', 'strikethrough','|', 'pasteplain', 'forecolor', 'fontfamily', 'fontsize','|', 'emotion', 'map', 'link', 'unlink'$pagebreak";
                break;
            case 1: // 默认
                $tool.= "'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'gmap', 'insertframe', 'insertcode', 'webapp', 'template', '|',
            'horizontal', 'date', 'time', 'spechars', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'preview', 'searchreplace', 'help', 'drafts'$pagebreak";
                break;
        }

        $str.= "
		<script name=\"data[$name]\" type=\"text/plain\" id=\"dr_$name\">$value</script>
		<script type=\"text/javascript\">
			var editorOption = {
				UEDITOR_HOME_URL: \"".$ueurl."\",
				UEDITOR_URL: \"/api/ueditor/\",
				serverUrl:\"".$ueurl."php/index.php?siteid=".SITE_ID."&\",
				toolbars: [
					[ $tool ]
				],
				lang: \"".SITE_LANGUAGE."\",
				initialContent:\"\",
				initialFrameWidth: \"{$width}\",
				initialFrameHeight: \"{$height}\",
				initialStyle:\"body{font-size:14px}\",
				wordCount:false,
				elementPathEnabled:false,
				autoFloatEnabled:".($cfg['option']['autofloat'] ? 'true' : 'false').",
				autoHeightEnabled:".($cfg['option']['autoheight'] ? 'true' : 'false').",
				charset:\"utf-8\",
				zIndex: \"1\",
				pageBreakTag:\"_page_break_tag_\"
			};
			var editor = new baidu.editor.ui.Editor(editorOption);
			editor.render(\"dr_$name\");
		</script>
		".$tips;

        return $this->input_format($name, $text, $str);
    }
}