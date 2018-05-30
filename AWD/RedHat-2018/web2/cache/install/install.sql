

DROP TABLE IF EXISTS `{dbprefix}1_block`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_block` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '资料块名称',
  `content` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='资料块表' AUTO_INCREMENT=4 ;

DROP TABLE IF EXISTS `{dbprefix}1_category`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_category` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='栏目表' AUTO_INCREMENT=22 ;


DROP TABLE IF EXISTS `{dbprefix}1_form`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_form` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `table` varchar(50) NOT NULL COMMENT '表名',
  `setting` text COMMENT '配置信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `table` (`table`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='表单模型表' AUTO_INCREMENT=3 ;


INSERT INTO `{dbprefix}1_form` VALUES(1, '留言', 'liuyan', '{"post":"1","code":"1","send":"","template":"","rt_url":""}');

DROP TABLE IF EXISTS `{dbprefix}1_form_liuyan`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_form_liuyan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '主题',
  `uid` int(10) unsigned DEFAULT '0' COMMENT '录入者uid',
  `author` varchar(100) DEFAULT NULL COMMENT '录入者账号',
  `inputip` varchar(30) DEFAULT NULL COMMENT '录入者ip',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0' COMMENT '排序值',
  `tableid` smallint(5) unsigned NOT NULL COMMENT '附表id',
  `neirong` mediumtext,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `inputtime` (`inputtime`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='留言表单表' AUTO_INCREMENT=3 ;



DROP TABLE IF EXISTS `{dbprefix}1_form_liuyan_data_0`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_form_liuyan_data_0` (
  `id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned DEFAULT '0' COMMENT '录入者uid',
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言表单附表';


DROP TABLE IF EXISTS `{dbprefix}1_index`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `mid` varchar(20) NOT NULL COMMENT '模块目录',
  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
  `status` tinyint(2) NOT NULL COMMENT '审核状态',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='内容索引表' AUTO_INCREMENT=127 ;



DROP TABLE IF EXISTS `{dbprefix}1_news`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_news` (
  `id` int(10) unsigned NOT NULL,
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `title` varchar(255) DEFAULT NULL COMMENT '主题',
  `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键字',
  `description` text COMMENT '描述',
  `hits` mediumint(8) unsigned DEFAULT NULL COMMENT '浏览数',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者id',
  `author` varchar(50) NOT NULL COMMENT '作者名称',
  `status` tinyint(2) NOT NULL COMMENT '状态',
  `url` varchar(255) DEFAULT NULL COMMENT '地址',
  `tableid` smallint(5) unsigned NOT NULL COMMENT '附表id',
  `inputip` varchar(15) DEFAULT NULL COMMENT '录入者ip',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL,
  `favorites` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`),
  KEY `updatetime` (`updatetime`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模型主表';

DROP TABLE IF EXISTS `{dbprefix}1_news_data_0`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_news_data_0` (
  `id` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `content` mediumtext COMMENT '内容',
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模型附表';


DROP TABLE IF EXISTS `{dbprefix}1_tag`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_tag` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='关键词库表' AUTO_INCREMENT=4 ;

INSERT INTO `{dbprefix}1_tag` VALUES(1, 0, '标签测试', 'test', NULL, 18, '', '', '1', 0, 0);
INSERT INTO `{dbprefix}1_tag` VALUES(2, 0, '中国', 'zhongguo', '', 0, '', '', '', 0, 0);


DROP TABLE IF EXISTS `{dbprefix}1_weixin`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_weixin` (
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信属性参数表';


DROP TABLE IF EXISTS `{dbprefix}1_weixin_follow`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_weixin_follow` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `status` (`status`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝同步表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{dbprefix}1_weixin_menu`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_weixin_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `value` text NOT NULL,
  `displayorder` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信菜单表' AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `{dbprefix}1_weixin_user`;
CREATE TABLE IF NOT EXISTS `{dbprefix}1_weixin_user` (
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

DROP TABLE IF EXISTS `{dbprefix}admin`;
CREATE TABLE IF NOT EXISTS `{dbprefix}admin` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `realname` varchar(50) DEFAULT NULL COMMENT '管理员姓名',
  `usermenu` text COMMENT '自定义面板菜单，序列化数组格式',
  `color` text COMMENT '定制权限',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员表' AUTO_INCREMENT=3 ;

INSERT INTO `{dbprefix}admin` VALUES(1, '网站创始人', '', 'blue');

DROP TABLE IF EXISTS `{dbprefix}attachment`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `siteid` tinyint(3) unsigned NOT NULL COMMENT '站点id',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `tableid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '附件副表id',
  `download` mediumint(8) NOT NULL DEFAULT '0' COMMENT '下载次数',
  `filesize` int(10) unsigned NOT NULL COMMENT '文件大小',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filemd5` varchar(50) NOT NULL COMMENT '文件md5值',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `author` (`author`),
  KEY `relatedtid` (`related`),
  KEY `fileext` (`fileext`),
  KEY `filemd5` (`filemd5`),
  KEY `siteid` (`siteid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表' AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `{dbprefix}attachment_0`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_0` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表0';


DROP TABLE IF EXISTS `{dbprefix}attachment_1`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_1` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表1';


DROP TABLE IF EXISTS `{dbprefix}attachment_2`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_2` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表2';


DROP TABLE IF EXISTS `{dbprefix}attachment_3`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_3` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表3';


DROP TABLE IF EXISTS `{dbprefix}attachment_4`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_4` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表4';


DROP TABLE IF EXISTS `{dbprefix}attachment_5`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_5` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表5';


DROP TABLE IF EXISTS `{dbprefix}attachment_6`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_6` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表6';


DROP TABLE IF EXISTS `{dbprefix}attachment_7`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_7` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表7';


DROP TABLE IF EXISTS `{dbprefix}attachment_8`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_8` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表8';


DROP TABLE IF EXISTS `{dbprefix}attachment_9`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_9` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表9';


DROP TABLE IF EXISTS `{dbprefix}field`;
CREATE TABLE IF NOT EXISTS `{dbprefix}field` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT '字段别名语言',
  `fieldname` varchar(50) NOT NULL COMMENT '字段名称',
  `fieldtype` varchar(50) NOT NULL COMMENT '字段类型',
  `relatedid` smallint(5) unsigned NOT NULL COMMENT '相关id',
  `relatedname` varchar(50) NOT NULL COMMENT '相关表',
  `isedit` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否可修改',
  `ismain` tinyint(1) unsigned NOT NULL COMMENT '是否主表',
  `issystem` tinyint(1) unsigned NOT NULL COMMENT '是否系统表',
  `ismember` tinyint(1) unsigned NOT NULL COMMENT '是否会员可见',
  `issearch` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否可搜索',
  `disabled` tinyint(1) unsigned NOT NULL COMMENT '禁用？',
  `setting` text NOT NULL COMMENT '配置信息',
  `displayorder` tinyint(3) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `list` (`relatedid`,`disabled`,`issystem`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='字段表' AUTO_INCREMENT=42 ;


INSERT INTO `{dbprefix}field` VALUES(8, '主题', 'title', 'Text', 4, 'module', 1, 1, 1, 1, 1, 0, '{"option":{"width":400,"fieldtype":"VARCHAR","fieldlength":"255"},"validate":{"xss":1,"required":1,"formattr":"onblur=\\"check_title();get_keywords(''keywords'');\\""}}', 0);
INSERT INTO `{dbprefix}field` VALUES(9, '缩略图', 'thumb', 'File', 4, 'module', 1, 1, 1, 1, 1, 0, '{"option":{"ext":"jpg,gif,png","size":10,"width":400,"fieldtype":"VARCHAR","fieldlength":"255"}}', 0);
INSERT INTO `{dbprefix}field` VALUES(10, '关键字', 'keywords', 'Text', 4, 'module', 1, 1, 1, 1, 1, 0, '{"option":{"width":400,"fieldtype":"VARCHAR","fieldlength":"255"},"validate":{"xss":1,"formattr":" data-role=\\"tagsinput\\""}}', 0);
INSERT INTO `{dbprefix}field` VALUES(11, '描述', 'description', 'Textarea', 4, 'module', 1, 1, 1, 1, 1, 0, '{"option":{"width":500,"height":60,"fieldtype":"VARCHAR","fieldlength":"255"},"validate":{"xss":1,"filter":"dr_clearhtml"}}', 0);
INSERT INTO `{dbprefix}field` VALUES(12, '内容', 'content', 'Ueditor', 4, 'module', 1, 0, 1, 1, 1, 0, '{"option":{"mode":1,"width":"90%","height":400},"validate":{"xss":1,"required":1}}', 0);
INSERT INTO `{dbprefix}field` VALUES(25, '内容', 'neirong', 'Ueditor', 1, 'form-1', 1, 1, 0, 1, 0, 0, '{"option":{"width":"100%","height":"200","autofloat":"0","autoheight":"0","autodown":"0","page":"0","mode":"1","tool":"''bold'', ''italic'', ''underline''","mode2":"1","tool2":"''bold'', ''italic'', ''underline''","mode3":"1","tool3":"''bold'', ''italic'', ''underline''","value":""},"validate":{"required":"1","pattern":"","errortips":"","xss":"1","check":"","filter":"","tips":"","formattr":""},"is_right":"0"}', 0);
INSERT INTO `{dbprefix}field` VALUES(23, '主题', 'title', 'Text', 1, 'form-1', 1, 1, 1, 1, 1, 0, '{"option":{"width":400,"fieldtype":"VARCHAR","fieldlength":"255"},"validate":{"xss":1,"required":1}}', 0);

DROP TABLE IF EXISTS `{dbprefix}linkage`;
CREATE TABLE IF NOT EXISTS `{dbprefix}linkage` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '菜单名称',
  `type` tinyint(1) unsigned NOT NULL,
  `code` char(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `module` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='联动菜单表' AUTO_INCREMENT=2 ;


INSERT INTO `{dbprefix}linkage` VALUES(1, '中国地区', 0, 'address');


DROP TABLE IF EXISTS `{dbprefix}linkage_data_1`;
CREATE TABLE IF NOT EXISTS `{dbprefix}linkage_data_1` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `site` mediumint(5) unsigned NOT NULL COMMENT '站点id',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `pids` varchar(255) DEFAULT NULL COMMENT '所有上级id',
  `name` varchar(30) NOT NULL COMMENT '栏目名称',
  `cname` varchar(30) NOT NULL COMMENT '别名',
  `child` tinyint(1) unsigned DEFAULT '0' COMMENT '是否有下级',
  `hidden` tinyint(1) unsigned DEFAULT '0' COMMENT '前端隐藏',
  `childids` text COMMENT '下级所有id',
  `displayorder` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cname` (`cname`),
  KEY `hidden` (`hidden`),
  KEY `list` (`site`,`displayorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='联动菜单数据表' AUTO_INCREMENT=35 ;


INSERT INTO `{dbprefix}linkage_data_1` VALUES(1, 1, 0, '0', '北京', 'beijing', 0, 0, '1', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(2, 1, 0, '0', '天津', 'tianjin', 0, 0, '2', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(3, 1, 0, '0', '上海', 'shanghai', 0, 0, '3', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(4, 1, 0, '0', '重庆', 'chongqing', 0, 0, '4', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(5, 1, 0, '0', '黑龙江', 'heilongjiang', 0, 0, '5', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(6, 1, 0, '0', '吉林', 'jilin', 0, 0, '6', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(7, 1, 0, '0', '辽宁', 'liaoning', 0, 0, '7', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(8, 1, 0, '0', '河北', 'hebei', 0, 0, '8', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(9, 1, 0, '0', '河南', 'henan', 0, 0, '9', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(10, 1, 0, '0', '山东', 'shandong', 0, 0, '10', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(11, 1, 0, '0', '江苏', 'jiangsu', 0, 0, '11', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(12, 1, 0, '0', '山西', 'shanxi', 0, 0, '12', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(13, 1, 0, '0', '陕西', 'shanxi1', 0, 0, '13', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(14, 1, 0, '0', '甘肃', 'gansu', 0, 0, '14', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(15, 1, 0, '0', '四川', 'sichuan', 0, 0, '15', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(16, 1, 0, '0', '青海', 'qinghai', 0, 0, '16', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(17, 1, 0, '0', '湖南', 'hunan', 0, 0, '17', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(18, 1, 0, '0', '湖北', 'hubei', 0, 0, '18', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(19, 1, 0, '0', '江西', 'jiangxi', 0, 0, '19', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(20, 1, 0, '0', '安徽', 'anhui', 0, 0, '20', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(21, 1, 0, '0', '浙江', 'zhejiang', 0, 0, '21', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(22, 1, 0, '0', '福建', 'fujian', 0, 0, '22', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(23, 1, 0, '0', '广东', 'guangdong', 0, 0, '23', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(24, 1, 0, '0', '广西', 'guangxi', 0, 0, '24', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(25, 1, 0, '0', '贵州', 'guizhou', 0, 0, '25', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(26, 1, 0, '0', '云南', 'yunnan', 0, 0, '26', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(27, 1, 0, '0', '海南', 'hainan', 0, 0, '27', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(28, 1, 0, '0', '内蒙古', 'neimenggu', 0, 0, '28', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(29, 1, 0, '0', '新疆', 'xinjiang', 0, 0, '29', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(30, 1, 0, '0', '宁夏', 'ningxia', 0, 0, '30', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(31, 1, 0, '0', '西藏', 'xicang', 0, 0, '31', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(32, 1, 0, '0', '香港', 'xianggang', 0, 0, '32', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(33, 1, 0, '0', '澳门', 'aomen', 0, 0, '33', 0);
INSERT INTO `{dbprefix}linkage_data_1` VALUES(34, 1, 0, '0', '台湾', 'taiwan', 0, 0, '34', 0);

DROP TABLE IF EXISTS `{dbprefix}mail_smtp`;
CREATE TABLE IF NOT EXISTS `{dbprefix}mail_smtp` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `port` mediumint(8) unsigned NOT NULL,
  `displayorder` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邮件账户表' AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `{dbprefix}member`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` char(40) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '加密密码',
  `salt` char(10) NOT NULL COMMENT '随机加密码',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `phone` char(20) NOT NULL COMMENT '手机号码',
  `avatar` varchar(255) NOT NULL COMMENT '头像地址',
  `money` decimal(10,2) unsigned NOT NULL COMMENT 'RMB',
  `freeze` decimal(10,2) unsigned NOT NULL COMMENT '冻结RMB',
  `spend` decimal(10,2) unsigned NOT NULL COMMENT '消费RMB总额',
  `score` int(10) unsigned NOT NULL COMMENT '虚拟币',
  `experience` int(10) unsigned NOT NULL COMMENT '经验值',
  `adminid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '管理组id',
  `groupid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '用户组id',
  `levelid` smallint(5) unsigned NOT NULL COMMENT '会员级别',
  `overdue` int(10) unsigned NOT NULL COMMENT '到期时间',
  `regip` varchar(15) NOT NULL COMMENT '注册ip',
  `regtime` int(10) unsigned NOT NULL COMMENT '注册时间',
  `randcode` mediumint(6) unsigned NOT NULL COMMENT '随机验证码',
  `ismobile` tinyint(1) unsigned DEFAULT NULL COMMENT '手机认证标识',
  PRIMARY KEY (`uid`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `groupid` (`groupid`),
  KEY `adminid` (`adminid`),
  KEY `phone` (`phone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员表' AUTO_INCREMENT=3 ;


INSERT INTO `{dbprefix}member` VALUES(1, '', 'admin', 'ac7cd59472be180b81c7551b92925f03', 'b3967a0e93', '1212', '12', '', 9999.00, 0.00, 0.00, 10000, 10000, 1, 3, 4, 0, '', 0, 0, 0);

DROP TABLE IF EXISTS `{dbprefix}member_data`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_data` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `complete` tinyint(1) unsigned NOT NULL COMMENT '完善资料标识',
  `is_auth` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '实名认证标识',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员表' AUTO_INCREMENT=2 ;


INSERT INTO `{dbprefix}member_data` VALUES(1, 1, 0);


DROP TABLE IF EXISTS `{dbprefix}member_oauth`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_oauth` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '会员uid',
  `oid` varchar(255) NOT NULL COMMENT 'OAuth返回id',
  `oauth` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `expire_at` int(10) unsigned NOT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `refresh_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员OAuth2授权表' AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `{dbprefix}module`;
CREATE TABLE IF NOT EXISTS `{dbprefix}module` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` text COMMENT '站点划分',
  `dirname` varchar(50) NOT NULL COMMENT '目录名称',
  `share` tinyint(1) unsigned DEFAULT NULL COMMENT '是否共享模块',
  `extend` tinyint(1) unsigned DEFAULT NULL COMMENT '是否是扩展模块',
  `sitemap` tinyint(1) unsigned DEFAULT NULL COMMENT '是否生成地图',
  `setting` text COMMENT '配置信息',
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '禁用？',
  `displayorder` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dirname` (`dirname`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模块表' AUTO_INCREMENT=10 ;



INSERT INTO `{dbprefix}module` VALUES(4, '{"name":"\\u6587\\u7ae0","urlrule":"4","search_title":"[\\u7b2c{page}\\u9875{join}][{keyword}{join}][{param}{join}]{modulename}{join}{SITE_NAME}","search_keywords":"","search_description":"","use":1}', 'news', 0, 0, 0, '', 0, 0);



DROP TABLE IF EXISTS `{dbprefix}site`;
CREATE TABLE IF NOT EXISTS `{dbprefix}site` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '站点名称',
  `domain` varchar(50) NOT NULL COMMENT '站点域名',
  `setting` text NOT NULL COMMENT '站点配置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='站点表' ;



INSERT INTO `{dbprefix}site` VALUES(1, 'FineCMS', '{site_url}', '{"SITE_CLOSE":"0","SITE_CLOSE_MSG":"\\u7f51\\u7ad9\\u5347\\u7ea7\\u4e2d....","SITE_NAME":"FineCMS","SITE_TIME_FORMAT":"Y-m-d H:i","SITE_LANGUAGE":"zh-cn","SITE_THEME":"default","SITE_TEMPLATE":"default","SITE_TIMEZONE":"8","SITE_DOMAINS":"","SITE_REWRITE":"6","SITE_MOBILE_OPEN":"1","SITE_MOBILE":"","SITE_SEOJOIN":"_","SITE_TITLE":"FineCMS\\u516c\\u76ca\\u8f6f\\u4ef6","SITE_KEYWORDS":"\\u514d\\u8d39cms,\\u5f00\\u6e90cms","SITE_DESCRIPTION":"\\u516c\\u76ca\\u8f6f\\u4ef6\\u4ea7\\u54c1\\u4ecb\\u7ecd","SITE_IMAGE_RATIO":"1","SITE_IMAGE_WATERMARK":"0","SITE_IMAGE_VRTALIGN":"top","SITE_IMAGE_HORALIGN":"left","SITE_IMAGE_VRTOFFSET":"","SITE_IMAGE_HOROFFSET":"","SITE_IMAGE_TYPE":"0","SITE_IMAGE_OVERLAY":"default.png","SITE_IMAGE_OPACITY":"","SITE_IMAGE_FONT":"default.ttf","SITE_IMAGE_COLOR":"","SITE_IMAGE_SIZE":"","SITE_IMAGE_TEXT":"","SITE_DOMAIN":"www.gyb.com","SITE_IMAGE_CONTENT":0}');


DROP TABLE IF EXISTS `{dbprefix}urlrule`;
CREATE TABLE IF NOT EXISTS `{dbprefix}urlrule` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '规则类型',
  `name` varchar(50) NOT NULL COMMENT '规则名称',
  `value` text NOT NULL COMMENT '详细规则',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='URL规则表' AUTO_INCREMENT=9 ;


INSERT INTO `{dbprefix}urlrule` VALUES(1, 3, '栏目规则测试', '{"share_list":"{dirname}-list.html","share_list_page":"{dirname}-list-{page}.html","share_show":"{dirname}-show-{id}.html","share_show_page":"{dirname}-show-{id}-{page}.html","share_search":"","share_search_page":"","tags":""}');
INSERT INTO `{dbprefix}urlrule` VALUES(2, 4, '站点URL测试', '{"share_list":"","share_list_page":"","share_show":"","share_show_page":"","share_search":"search.html","share_search_page":"search\\/{param}.html","tags":"tag\\/{tag}.html"}');


