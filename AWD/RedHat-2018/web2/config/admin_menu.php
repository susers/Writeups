<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

return array(

    array(
        'name' => '首页',
        'mark' => 'home',
        'icon' => 'fa fa-home',
        'menu' => array(
            array(
                'name' => '控制台',
                'mark' => 'home-home',
                'icon' => 'fa fa-home',
                'menu' => array(
                    array(
                        'name' => '后台首页',
                        'uri' => 'home/main',
                        'icon' => 'fa fa-home',
                    ),
                    array(
                        'name' => '资料修改',
                        'uri' => 'root/my',
                        'icon' => 'fa fa-user',
                    ),
                    array(
                        'name' => '错误日志',
                        'uri' => 'system/debug',
                        'icon' => 'fa fa-bug',
                    ),
                    array(
                        'name' => '操作日志',
                        'uri' => 'system/index',
                        'icon' => 'fa fa-calendar',
                    ),
                )
            ),


        )
    ),

    array(
        'name' => '设置',
        'mark' => 'cog',
        'icon' => 'fa fa-cog',
        'menu' => array(
            array(
                'name' => '网站设置',
                'mark' => 'cog-sys',
                'icon' => 'fa fa-cog',
                'menu' => array(
                    array(
                        'name' => '后台设置',
                        'uri' => 'system/config',
                        'icon' => 'fa fa-cog',
                    ),
                    array(
                        'name' => '网站设置',
                        'uri' => 'site/config',
                        'icon' => 'fa fa-cog',
                    ),
                    array(
                        'name' => '网站管理',
                        'uri' => 'site/index',
                        'icon' => 'fa fa-globe',
                    ),
                    array(
                        'name' => '内容模型',
                        'uri' => 'module/index',
                        'icon' => 'fa fa-cogs',
                    ),
                    array(
                        'name' => '网站表单',
                        'uri' => 'form/index',
                        'icon' => 'fa fa-tasks',
                    ),
                    array(
                        'name' => '邮件设置',
                        'uri' => 'mail/index',
                        'icon' => 'fa fa-envelope',
                    ),
                    array(
                        'name' => '会员设置',
                        'uri' => 'member_setting/index',
                        'icon' => 'fa fa-cog',
                    ),
                    array(
                        'name' => '会员字段',
                        'uri' => 'admin/field/index/rname/member/rid/0',
                        'icon' => 'fa fa-code',
                    ),
                    array(
                        'name' => '管理员管理',
                        'uri' => 'root/index',
                        'icon' => 'fa fa-user',
                    ),
                )
            ),

        )
    ),

    array(
        'name' => '内容',
        'mark' => 'content',
        'icon' => 'fa fa-th-large',
        'menu' => array(
            array(
                'name' => '内容管理',
                'mark' => 'content-content',
                'icon' => 'fa fa-th-large',
                'menu' => array(
                    array(
                        'name' => '栏目管理',
                        'uri' => 'category/index',
                        'icon' => 'fa fa-list',
                    ),
                    array(
                        'name' => '关键词库',
                        'uri' => 'tag/index',
                        'icon' => 'fa fa-tag',
                    ),
                    array(
                        'name' => '附件管理',
                        'uri' => 'attachment/index',
                        'icon' => 'fa fa-folder',
                    ),
                    array(
                        'name' => '自定义内容',
                        'uri' => 'block/index',
                        'icon' => 'fa fa-th-large',
                    ),
                    array(
                        'name' => '会员管理',
                        'uri' => 'member/index',
                        'icon' => 'fa fa-user',
                    ),
                )
            ),

        )
    ),

    array(
        'name' => '微信',
        'mark' => 'weixin',
        'icon' => 'fa fa-weixin',
        'menu' => array(
            array(
                'name' => '微信管理',
                'mark' => 'weixin-weixin',
                'icon' => 'fa fa-weixin',
                'menu' => array(

                    array(
                        'name' => '账号接入',
                        'uri' => 'weixin/index',
                        'icon' => 'fa fa-cog',
                    ),
                    array(
                        'name' => '自定义菜单',
                        'uri' => 'wmenu/index',
                        'icon' => 'fa fa-table',
                    ),
                    array(
                        'name' => '微信粉丝',
                        'uri' => 'wuser/index',
                        'icon' => 'fa fa-user',
                    ),
                )
            ),

        )
    ),





    array(
        'name' => '模板',
        'mark' => '',
        'icon' => 'fa fa-html5',
        'menu' => array(
            array(
                'name' => '网站模板',
                'icon' => 'fa fa-folder',
                'menu' => array(
                    array(
                        'name' => '电脑模板',
                        'uri' => 'tpl/index',
                        'icon' => 'fa fa-desktop',
                    ),
                    array(
                        'name' => '手机模板',
                        'uri' => 'tpl/mobile',
                        'icon' => 'fa fa-mobile',
                    ),
                    array(
                        'name' => '风格样式',
                        'uri' => 'theme/index',
                        'icon' => 'fa fa-css3',
                    ),
                )
            ),

        )
    ),


    array(
        'name' => '插件',
        'mark' => 'myapp',
        'icon' => 'fa fa-puzzle-piece',
        'menu' => array(
            array(
                'name' => '插件管理',
                'mark' => 'app',
                'icon' => 'fa fa-puzzle-piece',
                'menu' => array(
                    array(
                        'name' => 'URL规则',
                        'uri' => 'urlrule/index',
                        'icon' => 'fa fa-magnet',
                    ),
                    array(
                        'name' => '联动菜单',
                        'uri' => 'linkage/index',
                        'icon' => 'fa fa-windows',
                    ),
                    array(
                        'name' => '数据结构',
                        'uri' => 'db/index',
                        'icon' => 'fa fa-database',
                    ),
                )
            ),

        )
    ),


    array(
        'name' => '云服务',
        'mark' => 'mycloud',
        'icon' => 'fa fa-cloud',
        'menu' => array(
            array(
                'name' => '云服务',
                'mark' => 'mycloud-app',
                'icon' => 'fa fa-cloud',
                'menu' => array(
                    array(
                        'name' => '程序升级',
                        'uri' => 'upgrade/index',
                        'icon' => 'fa fa-refresh',
                    ),
                    array(
                        'name' => '插件商城',
                        'uri' => 'home/cjonline',
                        'icon' => 'fa fa-plug',
                    ),
                    array(
                        'name' => '使用文档',
                        'uri' => 'home/helponline',
                        'icon' => 'fa fa-book',
                    ),
                    array(
                        'name' => '我要提问',
                        'url' => 'http://www.dayrui.com/index.php?s=member&app=bbs&c=home&m=add&catid=48',
                        'icon' => 'fa fa-edit',
                    ),
                )
            ),

        )
    ),


);