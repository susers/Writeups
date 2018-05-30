<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */


/**
 * 多语言输出
 *
 * @param	多个参数
 * @return	string|NULL
 */
function fc_lang() {

    $param = func_get_args();
    if (empty($param)) {
        return NULL;
    }

    // 取第一个作为语言名称
    $string = $param[0];
    unset($param[0]);

    // 调用语言包内容
    $lang = get_instance()->lang->line($string);
    $string = $lang ? $lang : $string;
    
    // 替换
    $string = get_instance()->replace_lang($string);

    $string = $param ? vsprintf($string, $param) : $string;


    return $string;
}


/**
 * 多语言输出
 *
 * @param	多个参数
 * @return	string|NULL
 */
function dr_lang() {

    $param = func_get_args();
    if (empty($param)) {
        return NULL;
    }

    if (count($param) == 1) {
        $string = lang($param[0]);
        return $string;
    }

    // 取第一个作为语言名称
    $string = $param[0];
    unset($param[0]);


    return $string;
}

/**
 * 网站风格目录
 *
 * @return	string|NULL
 */
function dr_get_theme() {

    if (!function_exists('dr_dir_map')) {
        return array('default');
    }

    return array_diff(dr_dir_map(WEBPATH.'statics/', 1), array('avatar', 'admin', 'comment', 'emotions', 'js', 'oauth', 'watermark', 'space'));

}


/**
 * 获取6位数字随机验证码
 */
function dr_randcode() {
    return rand(100000, 999999);
}


/**
 * 删除目录及目录下面的所有文件
 * 
 * @param	string	$dir		路径
 * @return	bool	如果成功则返回 TRUE，失败则返回 FALSE
 */
function dr_dir_delete($dir) {

    $dir = str_replace('\\', '/', $dir);
    if (substr($dir, -1) != '/') {
        $dir = $dir . '/';
    }
    if (!is_dir($dir)) {
        return FALSE;
    }

    $list = glob($dir . '*');
    foreach ($list as $v) {
        is_dir($v) ? dr_dir_delete($v) : @unlink($v);
    }

    return @rmdir($dir);
}

/**
 * discuz加密/解密
 */
function dr_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

    if (!$string) {
        return '';
    }

    $ckey_length = 4;

    $key = md5($key ? $key : SYS_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result.= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

function dr_array2array($a1, $a2) {

    if (!$a1 || !$a2) {
        return array();
    } elseif ($a1 && !$a2) {
        return $a1;
    } elseif (!$a1 && $a2) {
        return $a2;
    }

    return array_merge($a1, $a2);

}


/**
 * 两数组覆盖合并
 */
function dr_array22array($a1, $a2) {

    $a = array();
    $a = $a1 ? $a1 : $a;
    if ($a2) {
        foreach ($a2 as $i => $t) {
            $a[$i] = $t;
        }
    }

    return $a;
}



/**
 * 调用会员详细信息（自定义字段需要手动格式化）
 *
 * @param	intval	$uid	会员uid
 * @param	intval	$cache	缓存时间
 * @return	string
 */
function dr_member_info($uid, $cache = -1) {

    $ci = &get_instance();
    $data = $ci->get_cache_data('member-info-'.$uid);
    if (!$data) {
        $data = $ci->member_model->get_member($uid);
        $ci->set_cache_data('member-info-'.$uid, $data, $cache > 0 ? $cache : SYS_CACHE_MEMBER);
    }

    return $data;
}



/**
 * 验证码图片获取
 */
function dr_code($width, $height, $url = '') {
    $url = '/index.php?c=api&m=captcha&width='.$width.'&height='.$height;
    return '<img align="absmiddle" style="cursor:pointer;" onclick="this.src=\''.$url.'&\'+Math.random();" src="'.$url.'" />';
}

/**
 * 排序操作
 */
function ns_sorting($name) {

    $value = $_GET['order'] ? $_GET['order'] : '';
    if (!$value) {
        return 'sorting';
    }

    if (strpos($value, $name) === 0 && strpos($value, 'asc') !== FALSE) {
        return 'sorting_asc';
    } elseif (strpos($value, $name) === 0 && strpos($value, 'desc') !== FALSE) {
        return 'sorting_desc';
    }

    return 'sorting';
}

/**
 * 移除order字符串
 */
function dr_member_order($url) {

    $data = @explode('&', $url);
    if ($data) {
        foreach ($data as $t) {
            if (strpos($t, 'order=') === 0) {
                $url = str_replace('&' . $t, '', $url);
            } elseif (strpos($t, 'action=') === 0) {
                $url = str_replace('&' . $t, '', $url);
            }
        }
    }

    return $url;
}

/**
 * 统计图表调用
 */
function dr_chart($file, $width, $height) {


}

/**
 * 百度地图调用
 */
function dr_baidu_map($value, $zoom = 5, $width = 600, $height = 400) {

    if (!$value) {
        return NULL;
    }

    $id = 'dr_map_'.rand(0, 99);
    $width = $width ? $width : '100%';
    list($lngX, $latY) = explode(',', $value);

    return '<script type=\'text/javascript\' src=\'http://api.map.baidu.com/api?v=1.4\'></script>
	<div id="' . $id . '" style="width:' . $width . 'px; height:' . $height . 'px; overflow:hidden"></div>
	<script type="text/javascript">
	var mapObj=null;
	lngX = "' . $lngX . '";
	latY = "' . $latY . '";
	zoom = "' . $zoom . '";		
	var mapObj = new BMap.Map("'.$id.'");
	var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
	mapObj.addControl(ctrl_nav);
	mapObj.enableDragging();
	mapObj.enableScrollWheelZoom();
	mapObj.enableDoubleClickZoom();
	mapObj.enableKeyboard();//启用键盘上下左右键移动地图
	mapObj.centerAndZoom(new BMap.Point(lngX,latY),zoom);
	drawPoints();
	function drawPoints(){
		var myIcon = new BMap.Icon("' . THEME_PATH . 'admin/images/mak.png", new BMap.Size(27, 45));
		var center = mapObj.getCenter();
		var point = new BMap.Point(lngX,latY);
		var marker = new BMap.Marker(point, {icon: myIcon});
		mapObj.addOverlay(marker);
	}
	</script>';
}

/**
 * 任意字段的选项值（用于options参数的字段，如复选框、下拉选择框、单选按钮）
 *
 * @param	intval	$id
 * @return	array
 */
function dr_field_options_id($id, $name = '') {

    $id = (int)$id;
    if (!$id) {
        return NULL;
    }

    $ci = &get_instance();
    $data = $ci->get_cache_data('field-info-'.$id);
    if (!$data) {
        $data = $ci->db->where('id', $id)->get('field')->row_array();
        if (!$data) {
            return NULL;
        }
        $data['setting'] = dr_string2array($data['setting']);
        $option = $data['setting']['option']['options'];
        if (!$option) {
            return NULL;
        }
        $data = explode(
            PHP_EOL,
            str_replace(
                array(chr(13), chr(10)),
                PHP_EOL,
                $option
            )
        );
        $return = array();
        foreach ($data as $t) {
            if ($t) {
                if (strpos($t, '|') !== FALSE) {
                    list($n, $v) = explode('|', $t);
                    $v = is_null($v) || !strlen($v) ? '' : trim($v);
                } else {
                    $v = $n = trim($t);
                }
                $return[$v] = trim($n);
            }
        }
        $ci->set_cache_data('field-info-'.$id, $return, 10000);
        return $return;
    }

    return $name && isset($data[$name]) ? $data[$name] : $data;
}

/**
 * 模型字段的选项值（用于options参数的字段，如复选框、下拉选择框、单选按钮）
 *
 * @param	string	$name
 * @param	intval	$catid
 * @param	string	$dirname
 * @return	array
 */
function dr_field_options($name, $catid = 0, $dirname = MOD_DIR) {

    if (!$name) {
        return NULL;
    }

    $module = get_module($dirname, SITE_ID);
    if (!$module) {
        return NULL;
    }

    $field = $catid && isset($module['category'][$catid]['field'][$name]) ? $module['category'][$catid]['field'][$name] : $module['field'][$name];
    if (!$field) {
        return NULL;
    }

    $option = $field['setting']['option']['options'];
    if (!$option) {
        return NULL;
    }

    $data = explode(
        PHP_EOL,
        str_replace(
            array(chr(13), chr(10)),
            PHP_EOL,
            $option
        )
    );
    $return = array();

    foreach ($data as $t) {
        if ($t) {
            if (strpos($t, '|') !== FALSE) {
                list($n, $v) = explode('|', $t);
                $v = is_null($v) || !strlen($v) ? '' : trim($v);
            } else {
                $v = $n = trim($t);
            }
            $return[$v] = trim($n);
        }
    }

    return $return;
}

/**
 * 资料块内容
 *
 * @param	intval	$id
 * @return	array
 */
function dr_block($id, $type = 0, $site = 0) {
    $ci = &get_instance();
    $site = $site ? $site : SITE_ID;
    return $ci->get_cache('block-'.$site, $id, $type);
}

/**
 * 联动菜单调用
 *
 * @param	string	$code	菜单代码
 * @param	intval	$id		菜单id
 * @param	intval	$level	调用级别，1表示顶级，2表示第二级，等等
 * @param	string	$name	菜单名称，如果有显示它的值，否则返回数组
 * @return	array
 */
function dr_linkage($code, $id, $level = 0, $name = '') {

    if (!$id) {
        return false;
    }

    $ci = &get_instance();
    $link = $ci->get_cache('linkage-'.SITE_ID.'-'.$code);
    $cids = $ci->get_cache('linkage-'.SITE_ID.'-'.$code.'-id');
    if (is_numeric($id)) {
        // id 查询
        $id = $cids[$id];
        $data = $link[$id];
    } else {
        // 别名查询
        $data = $link[$id];
    }
    $pids = @explode(',', $data['pids']);
    if ($level == 0) {
        return $name ? $data[$name] : $data;
    }

    if (!$pids) {
        return $name ? $data[$name] : $data;
    }

    $i = 1;
    foreach ($pids as $pid) {
        if ($pid) {
            $pid = $cids[$pid]; // 把id转化成cname
            if ($i == $level) {
                return $name ? $link[$pid][$name] : $link[$pid];
            }
            $i++;
        }
    }

    return $name ? $data[$name] : $data;
}

/**
 * 会员头像
 *
 * @param	intval	$uid
 * @param	string	$size
 * @return	string
 */
function dr_avatar($uid, $size = '45') {

    if ($uid) {
        if (defined('UCSSO_API')) {
            return ucsso_get_avatar($uid);
        }
        // 判断Ucenter公共头像
		$size = $size > 100 ? 180 : $size;
        foreach (array('png', 'jpg', 'gif', 'jpeg') as $ext) {
            if (is_file(SYS_UPLOAD_PATH.'/member/'.$uid.'/'.$size.'x'.$size.'.'.$ext)) {
                return SYS_ATTACHMENT_URL.'member/'.$uid.'/'.$size.'x'.$size.'.'.$ext;
            }
        }
    }

    return $size == 45 ? THEME_PATH.'admin/images/avatar_45.png' : THEME_PATH.'admin/images/avatar_90.png';
}


/**
 * 模型内容阅读量显示js
 *
 * @param	intval	$id
 * @return	string
 */
function dr_show_hits($id) {
    return "<span id=\"dr_show_hits_{$id}\">0</span><script type=\"text/javascript\">
                $.ajax({
                    type: \"GET\",
                    url:\"".SITE_URL."index.php?c=api&m=hits&module=".MOD_DIR."&id={$id}\",
                    dataType: \"jsonp\",
                    success: function(data){
			            $(\"#dr_show_hits_{$id}\").html(data.html);
                    },
                    error: function(){ }
                });
    </script>";
}


/**
 * 调用远程数据
 *
 * @param	string	$url
 * @return	string
 */
function dr_catcher_data($url) {

    // fopen模式
    if (ini_get('allow_url_fopen')) {
        $data = @file_get_contents($url);
        if ($data !== FALSE) {
            return $data;
        }
    }

    // curl模式
    if (function_exists('curl_init') && function_exists('curl_exec')) {
        $ch = curl_init($url);
        $data = '';
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    return NULL;
}

/**
 * 附件信息
 *
 * @param	intval	$id
 * @return  array
 */
function get_attachment($id) {

    if (!$id) {
        return NULL;
    }

    $ci = &get_instance();
    $info = $ci->get_cache_data("attachment-{$id}");
    if ($info) {
        // 附件缓存
        return $info;
    }

    $data = $ci->db->where('id', (int)$id)->get('attachment')->row_array();
    if (!$data) {
        return NULL;
    }

    $info = $ci->db->where('id', (int)$id)->get('attachment_'.(int)$data['tableid'])->row_array();
    if (!$info) {
        return NULL;
    }

    // 合并变量
    $info = $data + $info;
    $info['_attachment'] = trim($info['attachment'], '/');

    // 远程图片
    $url = $info['remote'] ? $ci->get_cache('attachment', $data['siteid'], 'data', $info['remote'], 'url') : '';
    $info['attachment'] = $url ? $url.'/'.$info['_attachment'] : dr_ck_attach($info['_attachment']);

	
    // 附件属性信息
    $attachinfo = dr_string2array($info['attachinfo']);

    // 验证图片是否具有高宽属性
    if (in_array($info['fileext'], array('jpg', 'gif', 'png'))
        && (!isset($attachinfo['width']) || !$attachinfo['width'])) {
        list($attachinfo['width'], $attachinfo['height']) = @getimagesize(dr_file($info['attachment']));
        // 更新到数据表
    }
    unset($info['attachinfo']);

    $info = $attachinfo ? $info + $attachinfo : $info;

    $ci->set_cache_data("attachment-{$id}", $info, SYS_CACHE_ATTACH); // 保存附件缓存

    return $info;
}

function dr_ck_attach($file) {

    if (!SYS_UPLOAD_DIR) {
        return $file;
    } elseif (strpos($file, SYS_UPLOAD_DIR) === 0) {
        return trim(str_replace(SYS_UPLOAD_DIR, '', $file), '/');
    } elseif (strpos($file, 'member/uploadfile/') === 0) {
        return trim(str_replace('member/uploadfile/', '', $file), '/');
    } else {
        return $file;
    }
}


/**
 * 调用缩略图函数
 */
function dr_image($id, $size = 0) {

    if (!$id) {
        return THEME_PATH.'admin/images/nopic.gif';
    }

    $info = get_attachment($id);
    if (!$info) {
        return THEME_PATH.'admin/images/nopic.gif';
    }

    // 远程图片
    if (isset($info['remote']) && $info['remote']) {
        $file = $info['attachment'];
    } else {
        $file = SYS_ATTACHMENT_URL.$info['attachment'];
    }

    if ($size) {
        return str_replace(
            basename($info['attachment']),
            basename($info['attachment'], '.'.$info['fileext']).'_'.$size.'.'.$info['fileext'],
            $file
        );
    } else {
        return $file;
    }
}


/**
 * 生成缩略图函数
 * @param  $img    图片路径
 * @param  $width  缩略图宽度
 * @param  $height 缩略图高度
 * @param  $autocut 是否自动裁剪 默认裁剪，当高度或宽度有一个数值为0是，自动关闭
 */
function dr_thumb2($img, $width = 100, $height = 100, $autocut = 1) {

    if (!$img) {
        return THEME_PATH.'admin/images/nopic.gif';
    }

    // 当图片是附件id
    if (is_numeric($img)) {
        $thumb_file = trim(SYS_THUMB_DIR, '/').'/'.md5("index.php?c=image&m=thumb&p=$img-$width-$height-$autocut").'.jpg';
        if (is_file(WEBPATH.$thumb_file)) {
            return SITE_URL.$thumb_file;
        }
        $ci = &get_instance();
        return $ci->html_thumb2("$img-$width-$height-$autocut");
    } else {
        return dr_file($img);
    }
}

/**
 * 图片显示
 *
 * @param	string	$img	图片id或者路径
 * @param	intval	$width	输出宽度
 * @param	intval	$height	输出高度
 * @param	intval	$water	是否水印
 * @param	intval	$size	缩略图尺寸
 * @return  url
 */
function dr_thumb($img, $width = NULL, $height = NULL, $water = 0, $size = 0) {


    if (!$img) {
        return THEME_PATH.'admin/images/nopic.gif';
    }

    if (is_numeric($img)) { // 表示附件id

        $thumb_file = trim(SYS_THUMB_DIR, '/').'/'.md5("$img-$width-$height-$water-$size").'.jpg';
        if (is_file(WEBPATH.$thumb_file)) {
            return SITE_URL.$thumb_file;
        }

        $ci = &get_instance();
        return $ci->html_thumb("$img-$width-$height-$water-$size");
    }

    $img = dr_file($img);

    return $img ? $img : THEME_PATH.'admin/images/nopic.gif';
}

/**
 * 下载文件
 *
 * @param	string	$id
 * @return  array
 */
function dr_down_file($id) {

    if (!$id) {
        return '';
    }

    if (is_numeric($id)) { // 表示附件id
        $info = get_attachment($id);
        if ($info) {
            return SITE_URL."index.php?s=member&c=api&m=file&id=$id";
        }
    }

    $file = dr_file($id);

    return $file ? $file : '';
}

/**
 * 文件真实地址
 *
 * @param	string	$id
 * @return  array
 */
function dr_get_file($id) {

    if (!$id) {
        return '';
    }

    if (is_numeric($id)) { // 表示附件id
        $info = get_attachment($id);
        $id = $info['attachment'] ? $info['attachment'] : '';
    }

    $file = dr_file($id);

    return $file ? $file : '';
}

/**
 * 完整的文件路径
 *
 * @param	string	$url
 * @return  string
 */
function dr_file($url) {

    if (!$url || strlen($url) == 1) {
        return NULL;
    } elseif (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
        return $url;
    } elseif (strpos($url, SITE_PATH) !== FALSE && SITE_PATH != '/') {
        return $url;
    } elseif (substr($url, 0, 1) == '/') {
        return SITE_URL.substr($url, 1);
    }

    return SYS_ATTACHMENT_URL . $url;
}

/**
 * 全局变量调用
 *
 * @param	string	$name	别名
 * @return
 */
function dr_var($name) {
    return  get_instance()->get_cache('sysvar', $name);
}

/**
 * 格式化自定义字段内容
 *
 * @param	string	$field	字段类型
 * @param	string	$value	字段值
 * @param	array	$cfg	字段配置信息
 * @param	string	$dirname模型目录
 * @return
 */
function dr_get_value($field, $value, $cfg = NULL, $dirname = NULL) {

    $ci = &get_instance();
    $ci->load->library('dfield', array($dirname ? $dirname : MOD_DIR));

    $obj = $ci->dfield->get($field);
    if (!$obj) {
        return $value;
    }

    return $obj->output($value, $cfg);
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function dr_safe_replace($string) {
    $string = str_replace('%20', '', $string);
    $string = str_replace('%27', '', $string);
    $string = str_replace('%2527', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace("{", '', $string);
    $string = str_replace('}', '', $string);
    return $string;
}

/**
 * 字符截取
 *
 * @param	string	$str
 * @param	intval	$length
 * @param	string	$dot
 * @return  string
 */
function dr_strcut($string, $length, $dot = '...') {

    $charset = 'utf-8';
    if (strlen($string) <= $length) {
        return $string;
    }

    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
    $strcut = '';

    if (strtolower($charset) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length)
                break;
        }
        if ($noc > $length)
            $n -= $tn;
        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut.= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }

    $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    return $strcut . $dot;
}

/**
 * 清除HTML标记
 *
 * @param	string	$str
 * @return  string
 */
function dr_clearhtml($str) {

    $str = str_replace(
        array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $str
    );

    $str = preg_replace("/\<[a-z]+(.*)\>/iU", "", $str);
    $str = preg_replace("/\<\/[a-z]+\>/iU", "", $str);
    $str = preg_replace("/{.+}/U", "", $str);
    $str = str_replace(array(chr(13), chr(10), '&nbsp;'), '', $str);
    $str = strip_tags($str);

    return trim($str);
}

/**
 * 模型缓存数据
 *
 * @param	string	$dirname	名称
 * @param	intval	$siteid		站点id
 * @return  array
 */
function get_module($dirname, $siteid = SITE_ID) {

    $ci = &get_instance();
    $ci->load->library('dcache');
    $data = $ci->get_cache('module'); // 全部模型
    if (!$data[$dirname]) {
        return array();
    }

    $data[$dirname]['category'] = $ci->get_cache('category-'.$siteid);

    return $data[$dirname];
}

/**
 * 随机颜色
 *
 * @return	string
 */
function dr_random_color() {

    $str = '#';

    for ($i = 0; $i < 6; $i++) {
        $randNum = rand(0, 15);
        switch ($randNum) {
            case 10: $randNum = 'A';
                break;
            case 11: $randNum = 'B';
                break;
            case 12: $randNum = 'C';
                break;
            case 13: $randNum = 'D';
                break;
            case 14: $randNum = 'E';
                break;
            case 15: $randNum = 'F';
                break;
        }
        $str.= $randNum;
    }

    return $str;
}

/**
 * 友好时间显示函数
 *
 * @param	int		$time	时间戳
 * @return	string
 */
function dr_fdate($sTime, $formt = 'Y-m-d') {

    if (!$sTime) {
        return '';
    }

    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date('z',$cTime)) - intval(date('z',$sTime));
    $dYear = intval(date('Y',$cTime)) - intval(date('Y',$sTime));

    //n秒前，n分钟前，n小时前，日期
    if ($dTime < 60 ) {
        if ($dTime < 10) {
            return '刚刚';
        } else {
            return intval(floor($dTime / 10) * 10).'秒前';
        }
    } elseif ($dTime < 3600 ) {
        return intval($dTime/60).'分钟前';
    } elseif( $dTime >= 3600 && $dDay == 0  ){
        return intval($dTime/3600).'小时前';
    } elseif( $dDay > 0 && $dDay<=7 ){
        return intval($dDay).'天前';
    } elseif( $dDay > 7 &&  $dDay <= 30 ){
        return intval($dDay/7).'周前';
    } elseif( $dDay > 30 ){
        return intval($dDay/30).'个月前';
    } elseif ($dYear==0) {
        return date('m月d日', $sTime);
    } else {
        return date($formt, $sTime);
    }
}

/**
 * 时间显示函数
 *
 * @param	int		$time	时间戳
 * @param	string	$format	格式与date函数一致
 * @param	string	$color	当天显示颜色
 * @return	string
 */
function dr_date($time = NULL, $format = SITE_TIME_FORMAT, $color = NULL) {

    $time = (int) $time;
    if (!$time) {
        return '';
    }

    $format = $format ? $format : SITE_TIME_FORMAT;
    $string = date($format, $time);
    if (strpos($string, '1970') !== FALSE) {
        return '';
    }

    return $color && $time >= strtotime(date('Y-m-d 00:00:00')) && $time <= strtotime(date('Y-m-d 23:59:59')) ? '<font color="' . $color . '">' . $string . '</font>' : $string;
}

/**
 * JSON数据输出
 *
 * @param	int				$status	状态
 * @param	string|array	$code	返回数据
 * @param	string|int		$id		表单名称|返回Id
 * @return	string
 */
function dr_json($status, $code = '', $id = 0, $rid = 0) {

    if (defined('IS_API_AUTH') && IS_API_AUTH) {
        $data = array(
            'msg' => $code,
            'field' => strpos($id, 'http') === 0 ? '' : $id,
            'code' => $status ? 1 : 0,
            'id' => (int)$rid,
        );
        $return = $_GET['return'];
        if ($return) {
            $temp = $data;
            $data = array();
            foreach ($temp as $i => $t) {
                $data[$i.'_'.$return] = $t;
            }
        }
        return json_encode($data);
    }

    return json_encode(array('status' => $status, 'code' => $code, 'id' => $id));
}


/**
 * 将对象转换为数组
 *
 * @param	object	$obj	数组对象
 * @return	array
 */
function dr_object2array($obj) {
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if ($_arr && is_array($_arr)) {
        foreach ($_arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? dr_object2array($val) : $val;
            $arr[$key] = $val;
        }
    }
    return $arr;
}

/**
 * 将字符串转换为数组
 *
 * @param	string	$data	字符串
 * @return	array
 */
function dr_string2array($data) {
    if (is_array($data)) {
        return $data;
    } elseif (!$data) {
        return array();
    } elseif (strpos($data, 'a:') === 0) {
        return unserialize(stripslashes($data));
    } else {
        return @json_decode($data, true);
    }
}

/**
 * 将数组转换为字符串
 *
 * @param	array	$data	数组
 * @return	string
 */
function dr_array2string($data) {
    return $data ? json_encode($data) : '';
}

/**
 * 递归创建目录
 *
 * @param	string	$dir	目录名称
 * @return	bool|void
 */
function dr_mkdirs($dir) {
    if (!$dir) {
        return FALSE;
    }
    if (!is_dir($dir)) {
        dr_mkdirs(dirname($dir));
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
    }
}

/**
 * 设置表单 input 或者 textarea 字段的值
 *
 * @param	string	$name	表单名称data[$name]
 * @param	string	$value	修改时的值$data[$name]
 * @return	string	
 */
function dr_set_value($name, $value = NULL) {
    return isset($_POST['data'][$name]) ? $_POST['data'][$name] : $value;
}

/**
 * 设置表单 select 字段的值
 *
 * @param	string	$name	表单名称data[$name]
 * @param	string	$value	修改时的值$data[$name]
 * @return	string	
 */
function dr_set_select($name, $value = NULL, $field = NULL, $default = FALSE) {
    $value = dr_set_value($name, $value);
    if ($value === NULL && $default == TRUE) {
        return ' selected';
    }
    if ($value == $field) {
        return ' selected';
    }
}

/**
 * 设置表单 radio 字段的值
 *
 * @param	string	$name		表单名称data[$name]
 * @param	string	$value		修改时的值$data[$name]
 * @param	string	$field		当前选项的value值
 * @param	string	$default	默认选中状态
 * @return	string|void
 */
function dr_set_radio($name, $value = NULL, $field = NULL, $default = FALSE) {
    $value = dr_set_value($name, $value);
    if ($value === NULL && $default == TRUE) {
        return ' checked';
    }
    if ($value == $field) {
        return ' checked';
    }
}

/**
 * 设置表单 checkbox 字段的值
 *
 * @param	string	$name		表单名称data[$name]
 * @param	array	$value		修改时的值$data[$name] 复选框为数组格式值
 * @param	string	$field		当前选项的value值
 * @param	string	$default	默认选中状态
 * @return	string|void
 */
function dr_set_checkbox($name, $value = NULL, $field = NULL, $default = FALSE) {
    $value = dr_set_value($name, $value);
    if ($value === NULL && $default == TRUE) {
        return ' checked';
    }
    if (@is_array($value) && in_array($field, $value)) {
        return ' checked';
    }
}

/**
 * 汉字转为拼音
 *
 * @param	string	$word
 * @return	string
 */
function dr_word2pinyin($word) {
    if (!$word) {
        return '';
    }
    $ci = &get_instance();
    $ci->load->library('pinyin');
    return $ci->pinyin->result($word);
}

/**
 * 格式化输出文件大小
 *
 * @param	int	$fileSize	大小
 * @param	int	$round		保留小数位
 * @return	string
 */
function dr_format_file_size($fileSize, $round = 2) {

    if (!$fileSize) {
        return 0;
    }

    $i = 0;
    $inv = 1 / 1024;
    $unit = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');

    while ($fileSize >= 1024 && $i < 8) {
        $fileSize *= $inv;
        ++$i;
    }

    $temp = sprintf("%.2f", $fileSize);
    $value = $temp - (int) $temp ? $temp : $fileSize;

    return round($value, $round) . $unit[$i];
}

/**
 * 关键字高亮显示
 *
 * @param	string	$string		字符串
 * @param	string	$keyword	关键字
 * @return	string
 */
function dr_keyword_highlight($string, $keyword) {
    return $keyword != '' ? str_ireplace($keyword, '<font color=red><strong>' . $keyword . '</strong></font>', $string) : $string;
}

function dollar($value, $include_cents = TRUE) {
    if (!$include_cents) {
        return "$" . number_format($value);
    } else {
        return "$" . number_format($value, 2, '.', ',');
    }
}

/**
 * Base64加密
 *
 * @param	string	$string
 * @return	string
 */
function dr_base64_encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
    return $data;
}

/**
 * Base64解密
 *
 * @param	string	$string
 * @return	string
 */
function dr_base64_decode($string) {
    $data = str_replace(array('-', '_'), array('+', '/'), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data.= substr('====', $mod4);
    }
    return base64_decode($data);
}

// 兼容老版本

/**
 * 将语言转为实际内容
 *
 * @param	array	$_name	语言名称
 * @param	string	$lang	语言名称
 * @return	string
 */
function dr_lang2name($_name, $lang = SITE_LANGUAGE) {

    if (!$_name) {
        return NULL;
    }

    $name = dr_string2array($_name);
    if (!$name) {
        return lang($_name);
    }

    return isset($name[$lang]) ? $name[$lang] : $name['zh-cn'];
}

/**
 * 将实际内容转为语言
 *
 * @param	string	$value	实际内容
 * @param	array	$data	原语言数据
 * @return	string
 */
function dr_name2lang($value, $data = array()) {

    if (!is_array($data)) {
        $data = dr_string2array($data);
    }

    if (!isset($data['zh-cn'])) {
        $data['zh-cn'] = $value;
    }
    $data[SITE_LANGUAGE] = $value;

    return dr_array2string($data);
}

/**
 * 将数组转化为xml格式
 *
 * @param	array	$arr		数组
 * @param	bool	$htmlon		是否开启html模式
 * @param	bool	$isnormal	是否不全空格
 * @param	intval	$level		当前级别
 * @return	string
 */
function dr_array2xml($arr, $htmlon = TRUE, $isnormal = FALSE, $level = 1) {
    $space = str_repeat("\t", $level);
    $string = $level == 1 ? "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<result>\r\n" : '';
    foreach ($arr as $k => $v) {
        if (!is_array($v)) {
            $string.= $space."<$k>".($htmlon ? '<![CDATA[' : '').$v.($htmlon ? ']]>' : '')."</$k>\r\n";
        } else {
            $name = is_numeric($k) ? 'item' . $k : $k;
            $string.= $space."<$name>\r\n".dr_array2xml($v, $htmlon, $isnormal, $level + 1).$space."</$name>\r\n";
        }
    }
    $string = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $string);
    return $level == 1 ? $string.'</result>' : $string;
}

if (!function_exists('gethostbyname')) {
    function gethostbyname($domain) {
        return $domain;
    }
}

/**
 *
 * 正则替换和过滤内容
 *
 * @param   $html
 */
function dr_preg_html($html){
    $p = array("/<[a|A][^>]+(topic=\"true\")+[^>]*+>#([^<]+)#<\/[a|A]>/",
        "/<[a|A][^>]+(data=\")+([^\"]+)\"[^>]*+>[^<]*+<\/[a|A]>/",
        "/<[img|IMG][^>]+(src=\")+([^\"]+)\"[^>]*+>/");
    $t = array('topic{data=$2}','$2','img{data=$2}');
    $html = preg_replace($p, $t, $html);
    $html = strip_tags($html, "<br/>");
    return $html;
}

// 过滤非法字段
function dr_get_order_string($str, $order) {

    if (substr_count($str, ' ') >= 2
        || strpos($str, '(') !== FALSE
        || strpos($str, 'undefined') === 0
        || strpos($str, ')') !== FALSE ) {
        return $order;
    }

    return $str ? $str : ($order ? $order : 'id desc');

}

// 文件上传临时目录
function dr_upload_temp_path() {

    if (function_exists('ini_get')) {
        $path = ini_get('upload_tmp_dir');
        if ($path) {
            return rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        }
    }

    return WEBPATH.'cache/file/';
}

// 兼容性判断
if (!function_exists('ctype_digit')) {
    function ctype_digit($num) {
        if (strpos($num, '.') !== FALSE) {
            return false;
        }
        return is_numeric($num);
    }
}

// 兼容性判断
if (!function_exists('ctype_alpha')) {
    function ctype_alpha($num) {
        if (strpos($num, '.') !== FALSE) {
            return false;
        }
        return is_numeric($num);
    }
}


// 提取tag
function dr_tag_list($keyword, $is_code = 0) {

    if (!$keyword) {
        return array();
    }

    $data = array();
    $array = explode(',', $keyword);
    foreach ($array as $t) {
        $t = trim($t);
        $t && $data[$t] = dr_tags_url($is_code ? dr_word2pinyin($t) : $t);
    }

    return $data;
}

// 获取栏目数据及自定义字段
function dr_cat_value($id, $name) {

    if (empty($id)) {
        return 'id为空';
    } elseif (empty($name)) {
        return '字段为空';
    }

    $ci = &get_instance();
    $category = $ci->get_cache('category-'.SITE_ID, $id);
    return $category[$name];
}

// ajax调用动态内容
function dr_ajax_html($id, $tpl, $params = array()) {

    $params = dr_array2string($params);
    if (strlen($params) > 100) {
        return '<font color="red">【'.$id.'】参数太多</font>';
    }

    $url = SITE_URL."index.php?c=api&m=html&name=".$tpl."&params=".$params;
    return "<script type=\"text/javascript\">
	    $.ajax({type: \"GET\", url: \"{$url}&\"+Math.random(), dataType:\"jsonp\",
            success: function (data) {
                $(\"#{$id}\").html(data.html);
            }
        });
	</script>";
}



// 获取当前模板目录
function dr_tpl_path($file) {
    
    $path = TPLPATH.(IS_MOBILE ? 'mobile/' : 'pc/'). (IS_MEMBER ? SITE_TEMPLATE.'/member/' : SITE_TEMPLATE.'/common/');
    if (is_file($path.$file)) {
        return $path.$file;
    }
    
    return false;
}

// 判断满足定向跳转的条件 1单页,2模型首页,3栏目页,4内容,5扩展
function dr_is_redirect($type, $url) {

    if (!defined('SITE_URL_301') || !SITE_URL_301) {
        return;
    }

    // 不调整的条件
    if (!$url || strpos($url, 'http') === FALSE) {
        return; // 为空时排除
    } elseif (IS_MOBILE || SITE_MOBILE === TRUE) {
        return; // 排除移动端
    } elseif ($type > 1 && defined('CT_HTML_FILE')) {
        return; // 排除生成
    } elseif (intval($_GET['page']) > 1) {
        return; // 排除分页
    } elseif (intval($_GET['cache']) > 1) {
        return; // 排除cache
    } elseif (SITE_FID && in_array($type, array(2, 3))) {
        return; // 排除分站
    }

    // 跳转
    $url != dr_now_url() && redirect($url, 'location', '301');

}

// 字段表单控件输出
function dr_field_form($id, $value = '', $html = '{value}') {

    $id = (int)$id;
    if (!$id) {
        return NULL;
    }

    $ci = &get_instance();
    $data = $ci->get_cache_data('field-value-'.$id);
    if (!$data) {
        $data = $ci->db->where('id', $id)->get('field')->row_array();
        if (!$data) {
            return NULL;
        }
        $data['setting'] = dr_string2array($data['setting']);
        $ci->set_cache_data('field-value-'.$id, $data, 10000);
    }

    $ci->load->library('Dfield', array(APP_DIR));
    $field = $ci->dfield->get($data['fieldtype']);
    if (!is_object($field)) {
        return NULL;
    }

    if ($html) {
        $field->set_input_format($html);
    }

    return preg_replace('/(<div class="on.+<\/div>)/U', '', $field->input($data['name'], $data['fieldname'], $data['setting'], $value, 0));
}

function dr_http_prefix($url) {
    return SYS_HTTPS  ? 'https://'.$url : 'http://'.$url;
}



// 处理带Emoji的数据，type=0表示写入数据库前的emoji转为HTML，为1时表示HTML转为emoji码
function dr_weixin_emoji($msg, $type = 1){
    if ($type == 0) {
        $msg = json_encode($msg);
    } else {
        $txt = json_decode($msg);
        if ($txt !== null) {
            $msg = $txt;
        }
    }
    return $msg;
}

/**
 * 过滤emoji表情
 * @param type $str
 * @return type
 */
function dr_clear_emoji($str){
    $tmpStr = json_encode($str); //暴露出unicode
    $tmpStr = preg_replace("#(\\\ud[0-9a-f]{3})#ie","", $tmpStr);
    $new_str = json_decode($tmpStr);
    return $new_str;
}


// 将同步代码转为数组
function dr_member_sync_url($string) {

    if (preg_match_all('/src="(.+)"/iU', $string, $match)) {
        return $match[1];
    }

    return array();
}


function dr_weixin_get_token() {

    $ci	= &get_instance();
    $cfg = $ci->get_cache('weixin-'.SITE_ID,'config');
    return $cfg['account'];
}

function dr_weixin_show_url($id) {

    return SITE_URL.'index.php?c=weixin&m=show&id='.$id;
}

// 微信端的错误码转中文解释
function dr_error_msg($return, $more_tips = '') {
    $msg = array (
        '-1' => '系统繁忙，此时请开发者稍候再试',
        '0' => '请求成功',
        '40001' => '获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口',
        '40002' => '不合法的凭证类型',
        '40003' => '不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID',
        '40004' => '不合法的媒体文件类型',
        '40005' => '不合法的文件类型',
        '40006' => '不合法的文件大小',
        '40007' => '不合法的媒体文件id',
        '40008' => '不合法的消息类型',
        '40009' => '不合法的图片文件大小',
        '40010' => '不合法的语音文件大小',
        '40011' => '不合法的视频文件大小',
        '40012' => '不合法的缩略图文件大小',
        '40013' => '不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写',
        '40014' => '不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
        '40015' => '不合法的菜单类型',
        '40016' => '不合法的按钮个数',
        '40017' => '不合法的按钮个数',
        '40018' => '不合法的按钮名字长度',
        '40019' => '不合法的按钮KEY长度',
        '40020' => '不合法的按钮URL长度',
        '40021' => '不合法的菜单版本号',
        '40022' => '不合法的子菜单级数',
        '40023' => '不合法的子菜单按钮个数',
        '40024' => '不合法的子菜单按钮类型',
        '40025' => '不合法的子菜单按钮名字长度',
        '40026' => '不合法的子菜单按钮KEY长度',
        '40027' => '不合法的子菜单按钮URL长度',
        '40028' => '不合法的自定义菜单使用用户',
        '40029' => '不合法的oauth_code',
        '40030' => '不合法的refresh_token',
        '40031' => '不合法的openid列表',
        '40032' => '不合法的openid列表长度',
        '40033' => '不合法的请求字符，不能包含\uxxxx格式的字符',
        '40035' => '不合法的参数',
        '40038' => '不合法的请求格式',
        '40039' => '不合法的URL长度',
        '40050' => '不合法的分组id',
        '40051' => '分组名字不合法',
        '40117' => '分组名字不合法',
        '40118' => 'media_id大小不合法',
        '40119' => 'button类型错误',
        '40120' => 'button类型错误',
        '40121' => '不合法的media_id类型',
        '40132' => '微信号不合法',
        '40137' => '不支持的图片格式',
        '41001' => '缺少access_token参数',
        '41002' => '缺少appid参数',
        '41003' => '缺少refresh_token参数',
        '41004' => '缺少secret参数',
        '41005' => '缺少多媒体文件数据',
        '41006' => '缺少media_id参数',
        '41007' => '缺少子菜单数据',
        '41008' => '缺少oauth code',
        '41009' => '缺少openid',
        '42001' => 'access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明',
        '42002' => 'refresh_token超时',
        '42003' => 'oauth_code超时',
        '43001' => '需要GET请求',
        '43002' => '需要POST请求',
        '43003' => '需要HTTPS请求',
        '43004' => '需要接收者关注',
        '43005' => '需要好友关系',
        '44001' => '多媒体文件为空',
        '44002' => 'POST的数据包为空',
        '44003' => '图文消息内容为空',
        '44004' => '文本消息内容为空',
        '45001' => '多媒体文件大小超过限制',
        '45002' => '消息内容超过限制',
        '45003' => '标题字段超过限制',
        '45004' => '描述字段超过限制',
        '45005' => '链接字段超过限制',
        '45006' => '图片链接字段超过限制',
        '45007' => '语音播放时间超过限制',
        '45008' => '图文消息超过限制',
        '45009' => '接口调用超过限制',
        '45010' => '创建菜单个数超过限制',
        '45015' => '回复时间超过限制',
        '45016' => '系统分组，不允许修改',
        '45017' => '分组名字过长',
        '45018' => '分组数量超过上限',
        '46001' => '不存在媒体数据',
        '46002' => '不存在的菜单版本',
        '46003' => '不存在的菜单数据',
        '46004' => '不存在的用户',
        '47001' => '解析JSON/XML内容错误',
        '48001' => 'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限',
        '50001' => '用户未授权该api',
        '50002' => '用户受限，可能是违规后接口被封禁',
        '61451' => '参数错误(invalid parameter)',
        '61452' => '无效客服账号(invalid kf_account)',
        '61453' => '客服帐号已存在(kf_account exsited)',
        '61454' => '客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)(invalid kf_acount length)',
        '61455' => '客服帐号名包含非法字符(仅允许英文+数字)(illegal character in kf_account)',
        '61456' => '客服帐号个数超过限制(10个客服账号)(kf_account count exceeded)',
        '61457' => '无效头像文件类型(invalid file type)',
        '61450' => '系统错误(system error)',
        '61500' => '日期格式错误',
        '61501' => '日期范围错误',
        '9001001' => 'POST数据参数不合法',
        '9001002' => '远端服务不可用',
        '9001003' => 'Ticket不合法',
        '9001004' => '获取摇周边用户信息失败',
        '9001005' => '获取商户信息失败',
        '9001006' => '获取OpenID失败',
        '9001007' => '上传文件缺失',
        '9001008' => '上传素材的文件类型不合法',
        '9001009' => '上传素材的文件尺寸不合法',
        '9001010' => '上传失败',
        '9001020' => '帐号不合法',
        '9001021' => '已有设备激活率低于50%，不能新增设备',
        '9001022' => '设备申请数不合法，必须为大于0的数字',
        '9001023' => '已存在审核中的设备ID申请',
        '9001024' => '一次查询设备ID数量不能超过50',
        '9001025' => '设备ID不合法',
        '9001026' => '页面ID不合法',
        '9001027' => '页面参数不合法',
        '9001028' => '一次删除页面ID数量不能超过10',
        '9001029' => '页面已应用在设备中，请先解除应用关系再删除',
        '9001030' => '一次查询页面ID数量不能超过50',
        '9001031' => '时间区间不合法',
        '9001032' => '保存设备与页面的绑定关系参数错误',
        '9001033' => '门店ID不合法',
        '9001034' => '设备备注信息过长',
        '9001035' => '设备申请参数不合法',
        '9001036' => '查询起始值begin不合法'
    );

    if ($more_tips) {
        $res = $more_tips . ': ';
    } else {
        $res = '';
    }
    if (isset ( $msg [$return ['errcode']] )) {
        $res .= $msg [$return ['errcode']];
    } else {
        $res .= $return ['errmsg'];
    }

    $res .= ', 返回码：' . $return ['errcode'];

    return $res;
}

function dr_post_data($url, $param, $is_file = false, $return_array = true) {
    if (! $is_file && is_array ( $param )) {
        $param = JSON ( $param );
    }
    if ($is_file) {
        $header [] = "content-type: multipart/form-data; charset=UTF-8";
    } else {
        $header [] = "content-type: application/json; charset=UTF-8";
    }

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
    curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
    curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
    curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    $res = curl_exec ( $ch );

    $flat = curl_errno ( $ch );
    if ($flat) {
        $data = curl_error ( $ch );
    }

    curl_close ( $ch );

    $return_array && $res = json_decode ( $res, true );

    return $res;
}

function dr_get_access_token() {

    $name = WEBPATH.'cache/data/access_token.'.SITE_ID;
    $data = @json_decode(@dr_catcher_data($name), true);
    if (isset($data['time']) && $data['time'] > SYS_TIME && isset($data['access_token']) && $data['access_token']) {
        return $data['access_token'];
    }

    $ci	= &get_instance();
    $cfg = $ci->get_cache('weixin-'.SITE_ID, 'config');
    !$cfg['key'] && $ci->admin_msg('没有配置账号参数');
    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$cfg['key'].'&secret='.$cfg['secret'];
    $data = json_decode(wx_get_https_json_data($url), true);
    if (!$data) {
        @unlink($name);
        $ci->admin_msg('获取access_token失败，请检查服务器是否支持远程链接');
    }
    if (isset($data['errmsg']) && $data['errmsg']) {
        @unlink($name);
        $ci->admin_msg('错误代码（'.$data['errcode'].'）：'.$data['errmsg']);
    }
    $data['time'] = SYS_TIME + $data['expires_in'];
    @file_put_contents($name, json_encode($data));

    return $data['access_token'];
}

function dr_weixin_jssdk() {

    $ci	= &get_instance();
    $ci->load->library('jssdk');
    $signPackage = $ci->jssdk->GetSignPackage();
    return $signPackage;
}

// 处理带Emoji的数据，type=0表示写入数据库前的emoji转为HTML，为1时表示HTML转为emoji码
function dr_deal_emoji($msg, $type = 1){
    if ($type == 0) {
        $msg = json_encode($msg);
    } else {
        $txt = json_decode($msg);
        if ($txt !== null) {
            $msg = $txt;
        }
    }
    return $msg;
}


 /**
  * 将数组转换为JSON字符串（兼容中文）
  */
function JSON($array) {
    arrayRecursive ( $array, 'urlencode', true );
    $json = json_encode ( $array );
    return urldecode ( $json );
}

/**
 * 使用特定function对数组中所有元素做处理
 */
function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
    static $recursive_counter = 0;
    if (++ $recursive_counter > 1000) {
        die ( 'possible deep recursion attack' );
    }
    foreach ( $array as $key => $value ) {
        if (is_array ( $value )) {
            arrayRecursive ( $array [$key], $function, $apply_to_keys_also );
        } else {
            $array [$key] = $function ( $value );
        }

        if ($apply_to_keys_also && is_string ( $key )) {
            $new_key = $function ( $key );
            if ($new_key != $key) {
                $array [$new_key] = $array [$key];
                unset ( $array [$key] );
            }
        }
    }
    $recursive_counter --;
}

// 转化为json
function dr_weixin_en_json($data) {
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        return urldecode(json_encode(dr_url_encode($data)));
    } else {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

/**
 * POST请求
 */
function dr_weixin_post($url, $params) {

    if (function_exists('curl_init')) { // curl方式
        $oCurl = curl_init();
        if (stripos($url, 'https://') !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $string = $params;
        if (is_array($params)) {
            $aPOST = array();
            foreach ($params as $key => $val){
                $aPOST[] = $key.'='.urlencode($val);
            }
            $string = join('&', $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, TRUE);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $string);
        $response = curl_exec($oCurl);
        curl_close($oCurl);
        return json_decode($response, true);
    } elseif (function_exists('stream_context_create')) { // php5.3以上
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params),
            )
        );
        $_opts = stream_context_get_params(stream_context_get_default());
        $context = stream_context_create(array_merge_recursive($_opts['options'], $opts));
        return json_decode(file_get_contents($url, false, $context), true);
    } else {
        return FALSE;
    }
}

// 从url获取json数据
function wx_get_https_json_data($url) {

    $response = @file_get_contents($url);
    if (!$response) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        if ($error=curl_error($ch)){
            return dr_return_data(0, $error);
        }
        curl_close($ch);
    }

    return $response;
}




/**
 * url 编码
 */
function dr_url_encode($str) {
    if (is_array($str)) {
        foreach($str as $key=>$value) {
            $str[urlencode($key)] = dr_url_encode($value);
        }
    } else {
        $str = urlencode($str);
    }

    return $str;
}