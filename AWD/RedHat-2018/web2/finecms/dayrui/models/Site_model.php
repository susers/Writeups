<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */
	
class Site_model extends M_Model {

	public $config;

    public function __construct() {
        parent::__construct();
		$this->config = array(
			'SITE_NAME'					=> '网站的名称',
			'SITE_DOMAIN'				=> '网站的域名',
			'SITE_DOMAINS'				=> '网站的其他域名',
			'SITE_MOBILE'				=> '移动端域名',
            'SITE_CLOSE'				=> '网站是否是关闭状态',
            'SITE_CLOSE_MSG'			=> '网站关闭时的显示信息',
			'SITE_LANGUAGE'				=> '网站的语言',
			'SITE_THEME'				=> '网站的主题风格',
			'SITE_TEMPLATE'				=> '网站的模板目录',
			'SITE_TIMEZONE'				=> '所在的时区常量',
			'SITE_TIME_FORMAT'			=> '时间显示格式，与date函数一致，默认Y-m-d H:i:s',
			'SITE_TITLE'				=> '网站首页SEO标题',
			'SITE_SEOJOIN'				=> '网站SEO间隔符号',
			'SITE_KEYWORDS'				=> '网站SEO关键字',
			'SITE_DESCRIPTION'			=> '网站SEO描述信息',
			'SITE_NAVIGATOR'			=> '网站导航信息，多个导航逗号分开',
            'SITE_MOBILE_OPEN'		    => '是否自动识别移动端并强制定向到移动端域名',
            'SITE_IMAGE_CONTENT'		=> '是否内容编辑器显示水印图片',
			'SITE_IMAGE_RATIO'			=> '是否宽度自动适应',
			'SITE_IMAGE_HTML'			=> '图片静态化',
            'SITE_URL_301'			    => '控制URL唯一301跳转的开关',

		);
    }
	
	/**
	 * 创建站点
	 *
	 * @return	id
	 */
	public function add_site($data) {
	
		if (!$data) {
            return NULL;
        }

        $data['setting']['SITE_THEME'] = SITE_THEME;
        $data['setting']['SITE_TEMPLATE'] = SITE_TEMPLATE;
		$data['setting']['SITE_TIME_FORMAT'] = 'Y-m-d H:i';

		$this->db->insert('site', array(
			'name' => $data['name'],
			'domain' => $data['domain'],
			'setting' => dr_array2string($data['setting'])
		));

		$id = $this->db->insert_id();


        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_block')."`");
		$this->db->query(trim("
		CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_block')."` (
		  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(100) NOT NULL COMMENT '资料块名称',
		  `content` text NOT NULL COMMENT '内容',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='资料块表';
		"));

        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_form')."`");
		$this->db->query(trim("
		CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_form')."` (
		  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(50) NOT NULL COMMENT '名称',
		  `table` varchar(50) NOT NULL COMMENT '表名',
		  `setting` text DEFAULT NULL COMMENT '配置信息',
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `table` (`table`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='表单模型表';
		"));

        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_tag')."`");
        $this->db->query(trim("
        CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_tag')."` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `pid` int(10) DEFAULT '0' COMMENT '父级id',
          `name` varchar(200) NOT NULL COMMENT '关键词名称',
          `code` varchar(200) NOT NULL COMMENT '关键词代码（拼音）',
          `pcode` varchar(255) DEFAULT NULL,
          `hits` mediumint(8) unsigned NOT NULL COMMENT '点击量',
          `url` varchar(255) DEFAULT NULL COMMENT '关键词url',
          `childids` varchar(255) NOT NULL COMMENT '子类集合',
          `content` text NOT NULL COMMENT '关键词描述',
          `total` int(10) NOT NULL COMMENT '点击数量',
          `displayorder` int(10) NOT NULL COMMENT '排序值',
          PRIMARY KEY (`id`),
          UNIQUE KEY `name` (`name`),
          KEY `letter` (`code`,`hits`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关键词库表';
        "));

        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_index')."`");
        $this->db->query(trim("
        CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_index')."` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `mid` varchar(20) NOT NULL COMMENT '模块目录',
          `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
          `uid` int(10) NOT NULL COMMENT '作者id',
          `status` int(10) NOT NULL COMMENT '状态',
          `inputtime` int(10) NOT NULL COMMENT '录入时间',
          PRIMARY KEY (`id`),
          KEY `mid` (`mid`),
          KEY `catid` (`catid`),
          KEY `status` (`status`),
          KEY `uid` (`uid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容索引表';
        "));

        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_category')."`");
        $this->db->query(trim("
        CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_category')."` (
          `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
          `tid` tinyint(1) NOT NULL COMMENT '栏目类型，0单页，1模块，2外链',
          `pid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
          `mid` varchar(20) NOT NULL COMMENT '模块目录',
          `pids` varchar(255) NOT NULL COMMENT '所有上级id',
          `name` varchar(30) NOT NULL COMMENT '栏目名称',
          `domain` varchar(50) NOT NULL COMMENT '绑定域名',
          `letter` char(1) NOT NULL COMMENT '首字母',
          `dirname` varchar(30) NOT NULL COMMENT '栏目目录',
          `pdirname` varchar(100) NOT NULL COMMENT '上级目录',
          `child` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有下级',
          `childids` text NOT NULL COMMENT '下级所有id',
          `pcatpost` tinyint(1) NOT NULL COMMENT '是否父栏目发布',
          `thumb` varchar(255) NOT NULL COMMENT '栏目图片',
          `show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
          `content` mediumtext NOT NULL COMMENT '单页内容',
          `permission` text COMMENT '会员权限',
          `setting` text NOT NULL COMMENT '属性配置',
          `displayorder` tinyint(3) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `mid` (`mid`),
          KEY `tid` (`tid`),
          KEY `show` (`show`),
          KEY `dirname` (`dirname`),
          KEY `module` (`pid`,`displayorder`,`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模块栏目表';
        "));


        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_weixin')."`");
        $this->db->query(trim("
        CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_weixin')."` (
          `name` varchar(50) NOT NULL,
          `value` text NOT NULL,
          PRIMARY KEY (`name`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信属性参数表';
        "));

        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_weixin_follow')."`");
        $this->db->query(trim("
        CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_weixin_follow')."` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `openid` varchar(255) NOT NULL,
          `status` tinyint(1) NOT NULL,
          `uid` int(10) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `status` (`status`),
          KEY `openid` (`openid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝同步表';
        "));

        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_weixin_menu')."`");
        $this->db->query(trim("
        CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_weixin_menu')."` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
          `pid` int(10) NOT NULL,
          `name` varchar(100) NOT NULL,
          `type` varchar(20) NOT NULL,
          `value` text NOT NULL,
          `displayorder` int(10) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信菜单表' AUTO_INCREMENT=1 ;
        "));

        $this->db->query("DROP TABLE IF EXISTS `".$this->db->dbprefix($id.'_weixin_user')."`");
        $this->db->query(trim("
        CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($id.'_weixin_user')."` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `uid` int(10) unsigned DEFAULT NULL COMMENT '会员id',
          `username` varchar(100) NOT NULL,
          `groupid` int(10) NOT NULL,
          `openid` varchar(50) NOT NULL COMMENT '唯一id',
          `nickname` text NOT NULL COMMENT '微信昵称',
          `sex` tinyint(1) unsigned DEFAULT NULL COMMENT '性别',
          `city` varchar(30) DEFAULT NULL COMMENT '城市',
          `country` varchar(30) DEFAULT NULL COMMENT '国家',
          `province` varchar(30) DEFAULT NULL COMMENT '省',
          `language` varchar(30) DEFAULT NULL COMMENT '语言',
          `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像地址',
          `subscribe_time` int(10) unsigned NOT NULL COMMENT '关注时间',
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `subscribe_time` (`subscribe_time`),
          KEY `openid` (`openid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝表' AUTO_INCREMENT=1 ;
        "));


        $module = $this->db->order_by('displayorder ASC,id ASC')->get('module')->result_array();
        if ($module) {
            foreach ($module as $t) {
                $this->install_table($t['dirname'], $id);
            }
        }

        return $id;
	}

    public function install_table($dirname, $siteid) {

        // 判断模块表是否存在
        if (!$this->db->table_exists($siteid.'_'.$dirname)) {
            $mark2 = $mark1 = 0;
            foreach ($this->site_info as $sid => $c) {
                if ($this->db->table_exists($sid.'_'.$dirname)) {
                    $mark1 = 1;
                    $sql = $this->db->query("SHOW CREATE TABLE `".$this->db->dbprefix($sid.'_'.$dirname)."`")->row_array();
                    $this->db->query(str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array($this->db->dbprefix($siteid.'_'.$dirname), 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    ));
                }
                if ($this->db->table_exists($sid.'_'.$dirname.'_data_0')) {
                    $mark2 = 1;
                    $sql = $this->db->query("SHOW CREATE TABLE `".$this->db->dbprefix($sid.'_'.$dirname)."_data_0`")->row_array();
                    $this->db->query(str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array($this->db->dbprefix($siteid.'_'.$dirname).'_data_0', 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    ));
                }
            }
            if (!$mark1 || !$mark2) {
                // 系统默认表
                foreach ($this->system_table as $table => $sql) {
                    $this->db->query(trim(str_replace('{tablename}', $this->db->dbprefix($siteid.'_'.$dirname).$table, $sql)));
                }
            }
        }

    }

	/**
	 * 修改站点
	 *
	 * @return	void
	 */
	public function edit_site($id, $data) {
	
		if (!$data || !$id) {
            return NULL;
        }

		$this->db->where('id', $id)->update('site', array(
			'name' => $data['name'],
			'domain' => $data['domain'],
			'setting' => dr_array2string($data['setting'])
		));
	}
	
	/**
	 * 站点
	 *
	 * @return	array|NULL
	 */
	public function get_site_data() {
	
		$_data = $this->db->order_by('id ASC')->get('site')->result_array();
		if (!$_data) {
            return NULL;
        }

		$data = array();
		foreach ($_data as $t) {
			$t['setting'] = dr_string2array($t['setting']);
			$t['setting']['SITE_NAME'] = $t['name'];
			$t['setting']['SITE_DOMAIN'] = $t['domain'];
			$data[$t['id']]	= $t;
		}

		return $data;
	}

	/**
	 * 站点信息
	 *
	 * @return	array|NULL
	 */
	public function get_site_info($id) {

		$data = $this->db->where('id', $id)->get('site')->row_array();
		if (!$data) {
            return NULL;
        }

        $data['setting'] = dr_string2array($data['setting']);
        $data['setting']['SITE_NAME'] = $data['name'];
        $data['setting']['SITE_DOMAIN'] = $data['domain'];

		return $data['setting'];
	}

    // 站点缓存
    public function cache() {

        $data = $this->get_site_data();
        $oldfile = directory_map(WEBPATH.'config/site/');
        foreach ($oldfile as $file) {
            @unlink(WEBPATH.'config/site/'.$file);
        }

        $this->load->library('dconfig');
        $this->ci->dcache->delete('siteinfo');
        $cache = $domain = $module_domain = array();

        // 站点域名归类和写入配置文件
        foreach ($data as $id => $t) {
            // 站点域名归类
            $t['domain'] && $domain[$t['domain']] = $id;
            // 站点的其他域名
            if ($t['setting']['SITE_DOMAINS']) {
                $arr = @explode(',', $t['setting']['SITE_DOMAINS']);
                if ($arr) {
                    foreach ($arr as $a) {
                        $a && $domain[$a] = $id;
                    }
                }
            }
            // 移动端域名归类
            $t['setting']['SITE_MOBILE'] && $domain[$t['setting']['SITE_MOBILE']] = $id;
            // 写入配置文件
            $this->dconfig->file(WEBPATH.'config/site/'.$id.'.php')->note('站点配置文件')->space(32)->to_require_one($this->config, $t['setting'], 1);
            // 写入缓存文件
            $cache[$id] = $t['setting'];
        }

        $this->ci->dcache->set('siteinfo', $cache);


        @unlink(WEBPATH.'config/module_domain.php');

        // 生成站点域名归属
        $this->dconfig->file(WEBPATH.'config/domain.php')->note('站点域名文件')->space(32)->to_require_one($domain);
        $this->dconfig->file(WEBPATH.'config/module_domain.php')->note('模型域名归类文件')->space(32)->to_require_one($module_domain);


    }
}