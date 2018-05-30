<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

// 补全url
function dr_url_prefix($url, $domain = '', $siteid = SITE_ID) {

    $ci	= &get_instance();

    // 指定域名判断
    !$domain && $domain = $siteid > 1 && $ci->site_info[$siteid]['SITE_PC'] ? (IS_PC ? $ci->site_info[$siteid]['SITE_PC'] : $ci->site_info[$siteid]['SITE_MURL']) : (IS_PC ? SITE_PC : SITE_M_URL);

    return strpos($url, 'http') === 0 ? $url : $domain.ltrim($url, '/');
}

// 地址前缀部分
function dr_uri_prefix($type, $mod, $cat = array(), $fid = 0, $site = SITE_ID) {

    // 默认主网站的地址
    $site_url = '/';
    if ($type == 'php') {
        return $site_url.'index.php?';
    } elseif ($type == 'cat_show_ext_php') {
        return $site_url.'index.php?';
    } elseif ($type == 'rewrite') {
        return $site_url;
    }
}

/**
 * 格式化复选框\单选框\选项值 字符串转换为数组
 */
function dr_format_option_array($value) {

    $data = array();

    if (!$value) {
        return $data;
    }

    $options = explode(PHP_EOL, str_replace(array(chr(13), chr(10)), PHP_EOL, $value));

    foreach ($options as $t) {
        if (strlen($t)) {
            $n = $v = '';
            if (strpos($t, '|') !== FALSE) {
                list($n, $v) = explode('|', $t);
                $v = is_null($v) || !strlen($v) ? '' : trim($v);
            } else {
                $v = $n = trim($t);
            }
            $data[$v] = $n;
        }
    }

    return $data;
}

/**
 * 伪静态代码处理
 *
 * @param	array	$params	参数数组
 * @return	string
 */
function dr_rewrite_encode($params, $join = '-', $field = array()) {

    if (!$params) {
        return '';
    }

    !$join && $join = '-';
    $field = array_flip(dr_format_option_array($field));
    $url = '';
    foreach ($params as $i => $t) {
        $i = isset($field[$i]) && $field[$i] ? $field[$i] : $i;
        $url.= $join.$i.$join.$t;
    }

    return trim($url, $join);
}

/**
 * 伪静态代码转换为数组
 *
 * @param	string	$params	参数字符串
 * @return	array
 */
function dr_rewrite_decode($params, $join = '-', $field = array()) {

    if (!$params) {
        return NULL;
    }

    $field = dr_format_option_array($field);
    !$join && $join = '-';

    $i = 0;
    $array = explode($join, $params);

    $return = array();
    foreach ($array as $k => $t) {
        $name = str_replace('$', '_', $t);
        $name = isset($field[$name]) && $field[$name] ? $field[$name] : $name;
        $i%2 == 0 && $return[$name] = isset($array[$k+1]) ? $array[$k+1] : '';
        $i ++;
    }

    return $return;
}
 


 
/**
 * 搜索url组合
 *
 * @param	array	$params		搜索参数数组
 * @param	string	$name		当前参数名称
 * @param	string	$value		当前参数值
 * @param	string	$urlrule	搜索url规则
 * @return	string
 */
function dr_search_url($params = NULL, $name = NULL, $value = NULL, $urlrule = NULL) {

    $params = is_array($params) ? $params : array();

	if ($name) {
		if (strlen($value)) {
			$params[$name] = $value;
		} else {
			unset($params[$name]);
		}
	}
	if (is_array($params)) {
		foreach ($params as $i => $t) {
			if (strlen($t) == 0) {
                unset($params[$i]);
            }
		}
	}

        // PC端
        $ci	= &get_instance();
    $rule = $ci->get_cache('urlrule', (int)SITE_REWRITE, 'value');
        if ($rule && $rule['share_search_page']) {
            //$data['param'] = dr_rewrite_encode($params);
            $data['param'] = dr_rewrite_encode($params);
            $url = ltrim($rule['share_search_page'], '/');
            // 兼容php5.5
            if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
                $rep = new php5replace($data);
                $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
                $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
                unset($rep);
            } else {
                $url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
                $url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
            }

            return dr_uri_prefix('rewrite', array(), array(), 0).$url;
        } else {

            return dr_uri_prefix('php', array(), array(), 0).trim('c=search&'.@http_build_query($params), '&');
        }
	
}

/**
 * tags的url
 *
 * @param	string	关键字
 * @return	string	地址
 */
function dr_tags_url($name, $page = 0) {

    if (!$name) {
        return '';
    }

    // PC端
    $ci	= &get_instance();
    $rule = $ci->get_cache('urlrule', (int)SITE_REWRITE, 'value');
    if ($rule && $rule['tags']) {
        $data['tag'] = $name;
        $url = ltrim($rule['tags'], '/');
        $rep = new \php5replace($data);
        $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
        $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
        unset($rep);
        return dr_uri_prefix('rewrite', array(), array(), 0).$url;
    } else {
        return dr_uri_prefix('php', array(), array(), 0).'c=tag&name='.$name.($page ? '&page='.$page : '');
    }
}



/**
 * 模型内容分页链接
 *
 * @param	string	$urlrule
 * @param	intval	$page
 * @return	string	地址
 */
function dr_content_page_url($urlrule, $page) {
	return str_replace('{page}', $page, $urlrule);
}

/**
 * 联动菜单包屑导航
 *
 * @param	string	$code	联动菜单代码
 * @param	intval	$id		id
 * @param	string	$symbol	间隔符号
 * @param	string	$url	url地址格式，必须存在{linkage}，否则返回不带url的字符串
 * @return	string
 */
function dr_linkagepos($code, $id, $symbol = ' > ', $url = NULL) {

	if (!$code || !$id) {
        return NULL;
    }
	
	$ci	= &get_instance();
	$url = $url ? urldecode($url) : NULL;
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

    $name = array();
	$pids = @explode(',', $data['pids']);


        foreach ($pids as $pid) {
            $pid && $name[] = $url ? "<a href=\"".str_replace('{linkage}', $cids[$pid], $url)."\">{$link[$cids[$pid]]['name']}</a>" : $link[$cids[$pid]]['name'];
        }
        $name[] = $url ? "<a href=\"".str_replace('{linkage}', $id, $url)."\">{$data['name']}</a>" : $data['name'];

	
	return implode($symbol, $name);
}

/**
 * 模型栏目面包屑导航
 *
 * @param	intval	$catid	栏目id
 * @param	string	$symbol	面包屑间隔符号
 * @param	string	$url	是否显示URL
 * @param	string	$html	格式替换
 * @return	string
 */
function dr_catpos($catid, $symbol = ' > ', $url = TRUE, $html= '') {

	if (!$catid) {
        return '';
    }

    $html = str_replace(array('[url]', '[name]'), array('{url}', '{name}'), $html);
	
	$ci	= &get_instance();
	$cat = $ci->get_cache('category-'.SITE_ID);
	if (!isset($cat[$catid])) {
        return '';
    }
	
	$name = array();
	$array = explode(',', $cat[$catid]['pids']);
	foreach ($array as $id) {
		if ($id && $cat[$id]) {
            $murl = $cat[$id]['url'];
			$name[] = $url ? ($html ? str_replace(array('{url}', '{name}'), array($murl, $cat[$id]['name']), $html): "<a href=\"{$murl}\">{$cat[$id]['name']}</a>") : $cat[$id]['name'];
		}
	}

    $murl = $cat[$catid]['url'];
	$name[] = $url ? ($html ? str_replace(array('{url}', '{name}'), array($murl, $cat[$catid]['name']), $html): "<a href=\"{$murl}\">{$cat[$catid]['name']}</a>") : $cat[$catid]['name'];
	
	return implode($symbol, $name);
}
 
/**
 * 模型栏目层次关系
 *
 * @param	array	$mod
 * @param	array	$cat
 * @param	string	$symbol
 * @return	string
 */
function dr_get_cat_pname($mod, $cat, $symbol = '_') {

	if (!$cat['pids']) {
        return $cat['name'];
    }

    $ci = &get_instance();
	$name = array();
	$array = explode(',', $cat['pids']);
    $category = $ci->get_cache('category-'.SITE_ID);
	
	foreach ($array as $id) {
        $id && $category[$id] && $name[] = $category[$id]['name'];
	}

	$name[] = $cat['name'];
	krsort($name);
	
	return implode($symbol, $name);
}


 



/**
 * 模型内容SEO信息
 *
 * @param	array	$mod
 * @param	array	$cat
 * @param	intval	$page
 * @return	array
 */
function dr_show_seo($data, $page = 1) {

	$seo = array();
    $ci = &get_instance();
    $category = $ci->get_cache('category-'.SITE_ID);
	$cat = $category[$data['catid']];
    $data['page'] = $page;
	$data['join'] = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
	$data['name'] = $data['catname'] = dr_get_cat_pname(null, $cat, $data['join']);
	
	$meta_title = $cat['setting']['seo']['show_title'] ? $cat['setting']['seo']['show_title'] : '['.fc_lang('第%s页', '{page}').'{join}]{title}{join}{name}{join}{modulename}{join}{SITE_NAME}';

	$meta_title = $page > 1 ? str_replace(array('[', ']'), '', $meta_title) : preg_replace('/\[.+\]/U', '', $meta_title);

	// 兼容php5.5
	if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $rep = new php5replace($data);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_title']);
        unset($rep);
	} else {
		extract($data);
		$seo['meta_title'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$\\1", $meta_title);
		$seo['meta_title'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_title']);
	}
	
	if (is_array($data['keywords'])) {
		foreach ($data['keywords'] as $key => $t) {
			$seo['meta_keywords'].= $key.',';
		}
		$seo['meta_keywords'] = trim($seo['meta_keywords'], ',');
	} else {
		$seo['meta_keywords'] = $data['keywords'];
	}

    $seo['meta_description'] = htmlspecialchars(dr_clearhtml($data['description']));

	return $seo;
}

/**
 * 模型栏目SEO信息
 *
 * @param	array	$mod
 * @param	array	$cat
 * @param	intval	$page
 * @return	array
 */
function dr_category_seo($cat, $page = 1) {

	$seo = array();
	$cat['page'] = $page;
	$cat['join'] = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
	$cat['name'] = $cat['catname'] = dr_get_cat_pname(null, $cat, $cat['join']);
	
	$meta_title = $cat['setting']['seo']['list_title'] ? $cat['setting']['seo']['list_title'] : '['.fc_lang('第%s页', '{page}').'{join}]{modulename}{join}{SITE_NAME}';
	
	$meta_title = $page > 1 ? str_replace(array('[', ']'), '', $meta_title) : preg_replace('/\[.+\]/U', '', $meta_title);

	
	// 兼容php5.5
	if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $rep = new php5replace($cat);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_title']);
        $seo['meta_keywords'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $cat['setting']['seo']['list_keywords']);
        $seo['meta_keywords'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_keywords']);
        $seo['meta_description'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $cat['setting']['seo']['list_description']);
        $seo['meta_description'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_description']);
        unset($rep);
	} else {
		$seo['meta_title'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$cat[\\1]", $meta_title);
		$seo['meta_title'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_title']);
		$seo['meta_keywords'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$cat[\\1]", $cat['setting']['seo']['list_keywords']);
		$seo['meta_keywords'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_keywords']);
		$seo['meta_description'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$cat[\\1]", $cat['setting']['seo']['list_description']);
		$seo['meta_description'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_description']);
	}

    $seo['meta_description'] = htmlspecialchars(dr_clearhtml($seo['meta_description']));

	return $seo;
}


/**
 * 模型搜索SEO信息
 *
 * @param	array	$mod
 * @param	array	$param
 * @param	intval	$page
 * @return	array
 */
function dr_search_seo($mod, $param, $page = 1) {

	$seo = array();
    $seo['meta_keywords'] = '';
	$data['page'] = $page > 1 ? $page : '';
    $data['join'] = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
    $ci = &get_instance();
    $category = $ci->get_cache('category-'.SITE_ID);
    $data['catname'] = $param['catid'] ? dr_get_cat_pname(null, $category[$param['catid']], $data['join']) : '';
    $param['mid'] = $mod['name'];

    $data['param'] = '';
    $data['keyword'] = '';
    if ($param['keyword']) {
        $data['keyword'] = $param['keyword'];
        $seo['meta_keywords'].= $data['keyword'].',';
        unset($param['keyword']);
    }
    $param_value = array();
    if ($param['catid']) {
        $param_value = explode(PHP_EOL, dr_get_cat_pname(null, $category[$param['catid']], PHP_EOL));
        unset($param['catid']);
    }
    if ($param) {
        foreach ($param as $name => $value) {
            $field = $mod['field'][$name];
            switch ($field['fieldtype']) {

                case 'Radio':
                case 'Select':
                    $opt = dr_format_option_array($field['setting']['option']['options']);
                    isset($opt[$value]) && $opt[$value] && $param_value[] = $opt[$value];
                    break;

                case 'Linkage':
                    $param_value[] = dr_linkagepos($field['setting']['option']['linkage'], $value, '');
                    break;

                default:
                    $value && $param_value[] = $value;
                    break;

            }
        }
    }
    if ($param_value) {
        $data['param'] = implode($data['join'], $param_value);
        $seo['meta_keywords'].= implode(',', $param_value).',';
    }

	$meta_title = $mod['site'][SITE_ID]['search_title'] ? $mod['site'][SITE_ID]['search_title'] : '['.fc_lang('第%s页', '{page}').'{join}][{keyword}{join}][{param}{join}]{modulename}{join}{SITE_NAME}';

    if (preg_match_all('/\[.*\{(.+)\}.*\]/U', $meta_title, $m)) {
        $new = '';
        $replace = '';
        foreach ($m[1] as $i => $field) {
            $replace.= $m[0][$i];
            if (isset($data[$field]) && strlen($data[$field])) {
                $new.= str_replace(array('[', ']'), '', $m[0][$i]);
            }
        }
        $meta_title = str_replace($replace, $new, $meta_title);
    }

	// 兼容php5.5
	if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $rep = new php5replace($data);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_title']);
        unset($rep);
	} else {
		$seo['meta_title'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$data[\\1]", $meta_title);
		$seo['meta_title'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_title']);
	}

    $seo['meta_title'] = str_replace($data['join'].$data['join'], $data['join'], $seo['meta_title']);

    $seo['meta_keywords'].= $mod['site'][SITE_ID]['meta_keywords'];
    $seo['meta_keywords'] = trim($seo['meta_keywords'], ',');
    $seo['meta_description'] = $mod['site'][SITE_ID]['search_description'];

	return $seo;
}

/**
 * 模型内容URL地址
 *
 * @param	array	$mod
 * @param	array	$data
 * @param	mod	$page
 * @return	string
 */
function dr_show_url($data, $page = NULL) {





    $page && $data['page'] = $page = is_numeric($page) ? max((int)$page, 1) : $page;

    $ci	= &get_instance();
    $cat = $ci->get_cache('category-'.SITE_ID, $data['catid']);
    $rule = $ci->get_cache('urlrule', (int)$cat['setting']['urlrule'], 'value');
	if ($rule && $rule['show']) {
		// URL模式为自定义，且已经设置规则
		$cat['pdirname'].= $cat['dirname'];
		$data['dirname'] = $cat['dirname'];
		$inputtime = isset($data['_inputtime']) ? $data['_inputtime'] : $data['inputtime'];
		$data['y'] = date('Y', $inputtime);
		$data['m'] = date('m', $inputtime);
		$data['d'] = date('d', $inputtime);
		$data['pdirname'] = str_replace('/', $rule['catjoin'], $cat['pdirname']);
		$url = ltrim($page ? $rule['show_page'] : $rule['show'], '/');
		// 兼容php5.5
		if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $rep = new php5replace($data);
            $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
            $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
            unset($rep);
		} else {
			$url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
			$url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
		}
		return dr_uri_prefix('rewrite', null, $cat, 0).$url;
	}

	return dr_uri_prefix('cat_show_ext_php', null, $cat, 0).'c=show&id='.$data['id'].($page ? '&page='.$page : '');
}



/**
 * 模型栏目URL地址
 *
 * @return	string
 */
function dr_category_url($data, $siteid, $page = 0) {

	if (!$data) {
        return '/';
    }

    $page && $data['page'] = $page = is_numeric($page) ? max((int)$page, 1) : $page;

    $ci	= &get_instance();
	$rule = isset($data['setting']['urlrule']) ? $ci->get_cache('urlrule', (int)$data['setting']['urlrule'], 'value') : 0;
	
	if ($rule && $rule['list']) {
		// URL模式为自定义，且已经设置规则
		$data['pdirname'].= $data['dirname'];
		$data['pdirname'] = str_replace('/', $rule['catjoin'], $data['pdirname']);
		$url = ltrim($page ? $rule['list_page'] : $rule['list'], '/');
		// 兼容php5.5
		if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $rep = new php5replace($data);
            $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
            $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
            unset($rep);
		} else {
			$url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
			$url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
		}

		return dr_uri_prefix('rewrite', null, $data, 0, $siteid) . $url;
	}


    return dr_uri_prefix('cat_show_ext_php', null, $data, 0, $siteid) . 'c=category&id='.(isset($data['id']) ? $data['id'] : 0).($page ? '&page='.$page : '');
}


// 模型URL
function dr_module_url($mod, $sid) {

    // 绑定域名的情况下
    if ($mod['site'][$sid]['domain']) {
        return dr_http_prefix($mod['site'][$sid]['domain'].'/');
    }

    $ci	= &get_instance();

    // 自定义规则的情况下
    $rule = $ci->get_cache('urlrule', (int)$mod['site'][$sid]['urlrule'], 'value');
    $domain = isset($ci->site_info[$sid]['SITE_PC']) && $ci->site_info[$sid]['SITE_PC'] ? $ci->site_info[$sid]['SITE_PC'] : SITE_URL;

    if ($rule['module']) {
        return $domain.str_replace('{modname}', $mod['dirname'], $rule['module']);
    }

    return $domain.'index.php?s='.$mod['dirname'];
}


/**
 * url函数
 *
 * @param	string	$url		URL规则，如home/index
 * @param	array	$query		相关参数
 * @return	string	项目入口文件.php?参数
 */
function dr_url($url, $query = array(), $self = SELF) {

	if (!$url) {
        return $self;
    }


	$url = strpos($url, 'admin') === 0 ? substr($url, 5) : $url;
	$url = trim($url, '/');

    // 判断是否后台首页
    if ($self != 'index.php' && ($url == 'home/index' || $url == 'home/home')) {
        return SELF;
    }

	$url = explode('/', $url);
	$uri = array();

	switch (count($url)) {
		case 1:
			$uri['c'] = 'home';
			$uri['m'] = $url[0];
			break;
		case 2:
			$uri['c'] = $url[0];
			$uri['m'] = $url[1];
			break;
		case 3:
			$uri['s'] = $url[0];
            // 非后台且非会员中心的模型地址

			$uri['c'] = $url[1];
			$uri['m'] = $url[2];
			break;
	}

    $query && $uri = @array_merge($uri, $query);

	return $self.'?'.@http_build_query($uri);
}

/**
 * 会员url函数
 *
 * @param	string	$url 	URL规则，如home/index
 * @param	array	$query	相关参数
 * @return	string	地址
 */
function dr_member_url($url = '', $query = array(), $self = 'index.php') {

	if (!$url || $url == 'home/index' || $url == '/') {
        return MEMBER_URL;
    }


	$url = strpos($url, 'admin') === 0 ? substr($url, 5) : $url;
	$url = trim($url, '/');
	$url = explode('/', $url);
	$uri = array('s' => 'member');
	
	switch (count($url)) {
		case 1:
			$uri['c'] = 'home';
			$uri['m'] = $url[0];
			break;
		case 2:
			$uri['c'] = $url[0];
			$uri['m'] = $url[1];
			break;
		case 3:
			$uri['s'] = $url[0];

			$uri['c'] = $url[1];
			$uri['m'] = $url[2];
			break;
	}

    $query && $uri = @array_merge($uri, $query);

    return SITE_URL.$self.'?'.@http_build_query($uri);
}

/**
 * 当前URL
 */
function dr_now_url() {

    $pageURL = 'http';
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && $pageURL.= 's';

    $pageURL.= '://';
    if (strpos($_SERVER['HTTP_HOST'], ':') !== FALSE) {
        $url = explode(':', $_SERVER['HTTP_HOST']);
        $url[0] ? $pageURL.= $_SERVER['HTTP_HOST'] : $pageURL.= $url[0];
    } else {
        $pageURL.= $_SERVER['HTTP_HOST'];
    }
    
    $pageURL.= $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];

    $ci	= &get_instance();
    return $ci->security->xss_clean($pageURL);
}

/**
 * dialog弹出框窗口的URL
 *
 * @param	string	$url	地址
 * @param	string	$func	指向函数，如add，edit等
 * @param	string	$cache	更新缓存地址
 * @return	string
 */
function dr_dialog_url($url, $func) {
	return "javascript:dr_dialog('{$url}', '{$func}');";
}

// php 5.5 以上版本的正则替换方法
class php5replace {

    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    // 替换常量值 for php5.5
    public function php55_replace_var($value) {
        $v = '';
        @eval('$v = '.$value[1].';');
        return $v;
    }

    // 替换数组变量值 for php5.5
    public function php55_replace_data($value) {
        return $this->data[$value[1]];
    }

    // 替换函数值 for php5.5
    public function php55_replace_function($value) {

        if (function_exists($value[1])) {
            if ($value[2] == '$data') {
                $param = $this->data;
            } else {
                $param = $value[2];
            }
            return call_user_func_array($value[1], is_array($param) ? $param : @explode(',', $param));
        }

        return $value[0];
    }

}
