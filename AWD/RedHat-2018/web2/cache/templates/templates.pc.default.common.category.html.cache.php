<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
<link href="<?php echo HOME_THEME_PATH; ?>css/home.css" rel="stylesheet" />
<script src="<?php echo HOME_THEME_PATH; ?>js/home.js"></script>
<!-- 主体 -->
<div class="blog-body">

    <!-- 这个一般才是真正的主体内容 -->
    <div class="blog-container">
        <blockquote class="layui-elem-quote sitemap layui-breadcrumb shadow">
            <a href="/" title="网站首页">网站首页</a>
            <?php echo dr_catpos($catid, '', true, '<a href="[url]">[name]</a>'); ?>
            <a><cite>列表</cite></a>
        </blockquote>

        <div class="blog-main">

            <div class="blog-main-left">

                <?php $rt_c = $this->list_tag("action=category pid=$catid tid=1  return=c"); if ($rt_c) extract($rt_c); $count_c=count($return_c); if (is_array($return_c)) { foreach ($return_c as $key_c=>$c) { ?>
                <div class="blog-module shadow">
                    <div class="blog-module-title"><a href="<?php echo $c['url']; ?>"><?php echo $c['name']; ?></a></div>
                    <div class="index-div-ul">
                        <ul class="fa-ul blog-module-ul index-ul">
                            <?php $rt = $this->list_tag("action=module catid=$c[id] order=displayorder,updatetime num=16"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                            <li><a href="<?php echo $t['url']; ?>"><?php echo dr_strcut($t['title'], 40); ?></a></li>
                            <?php } } ?>
                        </ul>

                    </div>
                </div>
                <?php } } ?>


            </div>
            <!--右边小栏目-->
            <div class="blog-main-right">


                <div class="article-category shadow">
                    <div class="article-category-title">分类导航</div>

                    <!--循环同级栏目或者子栏目-->
                    <?php if (is_array($related)) { $count=count($related);foreach ($related as $c) { ?>
                    <a href="<?php echo $c['url']; ?>"><?php echo $c['name']; ?></a>
                    <?php } } ?>
                    <div class="clear"></div>
                </div>

                <div class="blog-module shadow">
                    <div class="blog-module-title">本类热门</div>
                    <ul class="fa-ul blog-module-ul">
                        <?php $rt = $this->list_tag("action=module catid=$catid order=hits num=15"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                        <li><i class="fa-li fa fa-hand-o-right"></i><a href="<?php echo $t['url']; ?>"><?php echo dr_strcut($t['title'], 35); ?></a></li>
                        <?php } } ?>
                    </ul>
                </div>

                <div class="blog-module shadow">
                    <div class="blog-module-title">随便看看</div>
                    <ul class="fa-ul blog-module-ul">
                        <?php $rt = $this->list_tag("action=module catid=$catid order=rand num=15"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                        <li><i class="fa-li fa fa-hand-o-right"></i><a href="<?php echo $t['url']; ?>"><?php echo dr_strcut($t['title'], 35); ?></a></li>
                        <?php } } ?>
                    </ul>
                </div>

                <div class="blog-module shadow">
                    <div class="blog-module-title">站内搜索</div>
                    <div class="blogroll" style="padding: 10px 0">
                        <form method="get" action="/index.php" class="form-horizontal" role="form" >
                            <input type="hidden" name="c" value="search">
                            <div class="input-group">
								<span class="input-group-btn">
									<select name="mid" class="form-control input-xsmall" style="margin-right: 10px">
										<?php $rt = $this->list_tag("action=cache name=module"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
										<option value="<?php echo $t['dirname']; ?>"><?php echo $t['name']; ?></option>
										<?php } } ?>
									</select>
								</span>
                                <input name="keyword" type="text" class="form-control">
                                <span class="input-group-btn">
									<button class="btn blue" type="submit">搜索</button>
								</span>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="blog-module shadow">
                    <div class="blog-module-title">tag标签</div>
                    <ul class="blogroll">
                        <?php $rt = $this->list_tag("action=tags order=rand num=30"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                        <li><a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a></li>
                        <?php } } ?>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>