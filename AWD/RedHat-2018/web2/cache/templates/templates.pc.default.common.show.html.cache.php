<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
<link href="<?php echo HOME_THEME_PATH; ?>css/detail.css" rel="stylesheet" />
<div class="blog-body">
    <div class="blog-container">
        <blockquote class="layui-elem-quote sitemap layui-breadcrumb shadow">
            <a href="/" title="网站首页">网站首页</a>
            <?php echo dr_catpos($catid, '', true, '<a href="[url]">[name]</a>'); ?>
            <a><cite>列表</cite></a>
        </blockquote>


        <div class="blog-main">
            <div class="blog-main-left">

                <div class="article-detail shadow">
                    <div class="article-detail-title">
                        <?php echo $title; ?>
                    </div>
                    <div class="article-detail-info">
                        <span>编辑时间：<?php echo $updatetime; ?></span>
                        <span>作者：<?php echo $author; ?></span>
                        <span>浏览量：<?php echo dr_show_hits($id); ?>次</span>
                    </div>
                    <div class="article-detail-content">
                       <?php echo $content; ?>
                    </div>
                    <div class="blog-single-foot">
                        <div class="blog-post-tags">
                            <?php if (is_array($keyword_list)) { $count=count($keyword_list);foreach ($keyword_list as $name=>$url) { ?>
                                <a href="<?php echo $url; ?>" target="_blank"><?php echo $name; ?></a>
                            <?php } } ?>
                        </div>
                        <p class="f14" style="margin-top: 10px">
                            <strong>上一篇：</strong><?php if ($prev_page) { ?><a href="<?php echo $prev_page['url']; ?>"><?php echo $prev_page['title']; ?></a><?php } else { ?>没有了<?php } ?><br>
                            <strong>下一篇：</strong><?php if ($next_page) { ?><a href="<?php echo $next_page['url']; ?>"><?php echo $next_page['title']; ?></a><?php } else { ?>没有了<?php } ?>
                        </p>
                    </div>
                </div>



            </div>
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
                    <div class="blog-module-title">相关文章</div>
                    <ul class="fa-ul blog-module-ul">
                        <!--此标签用于调用相关文章，tag=关键词1,关键词2，多个关键词,分隔，num=显示条数，field=显示字段-->
                        <?php $rt = $this->list_tag("action=related id=$id catid=$catid field=title,url,updatetime tag=$tag num=10"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                        <li><i class="fa-li fa fa-hand-o-right"></i><a href="<?php echo $t['url']; ?>"><?php echo dr_strcut($t['title'], 35); ?></a></li>
                        <?php } } ?>
                    </ul>
                </div>

                <div class="blog-module shadow">
                    <div class="blog-module-title">本类热门</div>
                    <ul class="fa-ul blog-module-ul">
                        <?php $rt = $this->list_tag("action=module catid=$catid order=hits num=10"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                        <li><i class="fa-li fa fa-hand-o-right"></i><a href="<?php echo $t['url']; ?>"><?php echo dr_strcut($t['title'], 35); ?></a></li>

                        <?php } } ?>
                    </ul>
                </div>
                <div class="blog-module shadow">
                    <div class="blog-module-title">随便看看</div>
                    <ul class="fa-ul blog-module-ul">
                        <?php $rt = $this->list_tag("action=module catid=$catid order=rand num=10"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                        <li><i class="fa-li fa fa-hand-o-right"></i><a href="<?php echo $t['url']; ?>"><?php echo dr_strcut($t['title'], 35); ?></a></li>
                        <?php } } ?>
                    </ul>
                </div>

            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>