<?php $indexc=1; if ($fn_include = $this->_include("header.html", "/")) include($fn_include); ?>
<link href="<?php echo HOME_THEME_PATH; ?>css/home.css" rel="stylesheet" />
<!-- 主体 -->
<div class="blog-body">

    <!-- 这个一般才是真正的主体内容 -->
    <div class="blog-container">
        <blockquote class="layui-elem-quote sitemap layui-breadcrumb shadow">
            <a href="/" title="网站首页">网站首页</a>
            <a><cite>会员中心</cite></a>
        </blockquote>
        <div class="blog-main">

            <div class="blog-main-right" style="float: left">
                <div class="blogerinfo shadow">

                    <div class="blogerinfo-figure">
                        <a href="<?php echo MEMBER_URL; ?>">
                            <img src="<?php echo $member['avatar_url']; ?>" />
                        </a>
                    </div>
                    <p class="blogerinfo-nickname"><?php echo $member['username']; ?></p>
                    <p class="blogerinfo-location"><?php echo $member['groupname']; ?></p>
                    <hr />
                    <div class="blogerinfo-contact">
                        <a   href="<?php echo MEMBER_URL; ?>">会员中心</a>
                        <a  href="<?php echo dr_member_url('login/out'); ?>">退出登录</a>
                    </div>

                </div>
                <div></div><!--占位-->


                <div class="article-category shadow">
                    <div class="article-category-title">会员菜单</div>

                    <a href="<?php echo dr_member_url('home/index'); ?>">资料修改</a>
                    <a href="<?php echo dr_member_url('account/password'); ?>">密码修改</a>
                    <a href="<?php echo dr_member_url('account/avatar'); ?>">上传头像</a>
                    <div class="clear"></div>
                </div>


            </div>
            <div class="blog-main-left" style="float: right">