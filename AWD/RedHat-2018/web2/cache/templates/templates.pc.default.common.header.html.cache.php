<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; Charset=utf-8">
    <title><?php echo $meta_title; ?></title>
    <meta name="keywords" content="<?php echo $meta_keywords; ?>" />
    <meta name="description" content="<?php echo $meta_description; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo HOME_THEME_PATH; ?>layui/css/layui.css" rel="stylesheet" />
    <link href="<?php echo HOME_THEME_PATH; ?>css/global.css" rel="stylesheet" />
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!--关键JS开始-->
    <script src="<?php echo HOME_THEME_PATH; ?>layui/layui.js"></script>
    <script src="<?php echo HOME_THEME_PATH; ?>js/global.js"></script>
    <script type="text/javascript">var memberpath = "<?php echo MEMBER_PATH; ?>";</script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/<?php echo SITE_LANGUAGE; ?>.js"></script>
    <link rel="stylesheet" href="<?php echo THEME_PATH; ?>js/ui-dialog.css">
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dialog-plus.js"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.artDialog.js?skin=default"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dayrui.js"></script>
    <!--关键js结束-->
</head>
<body>
<!-- 导航 -->
<nav class="blog-nav layui-header">
    <div class="blog-container">
        <!-- 登陆 -->
        <?php if ($member) { ?>
        <a href="<?php echo MEMBER_URL; ?>" class="blog-user">
            <img src="<?php echo $member['avatar_url']; ?>" />
        </a>
        <?php } else { ?>
        <div class="blog-user">
            <a href="<?php echo dr_member_url('register/index'); ?>">注册</a>
            |
            <a href="<?php echo dr_member_url('login/index'); ?>">登录</a>
        </div>
        <?php } ?>
        <!-- 调用站点首页 -->
        <a class="blog-logo" href="/"><img src="/statics/logo.png" /></a>
        <!-- 导航菜单 -->
        <ul class="layui-nav" lay-filter="nav">
            <li class="layui-nav-item <?php if ($indexc) { ?>layui-this<?php } ?>">
                <a href="/">首页</a>
            </li>
            <?php $rt = $this->list_tag("action=category pid=0"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
            <li class="layui-nav-item <?php if (in_array($catid, $t['catids'])) { ?>layui-this<?php } ?>">
                <a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a>
            </li>
            <?php } } ?>


        </ul>
        <!-- 手机和平板的导航开关 -->
        <a class="blog-navicon" href="javascript:;">
            <i class="fa fa-navicon"></i>
        </a>
    </div>
</nav>