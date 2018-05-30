<!-- 底部 -->
<footer class="blog-footer">
    <p><span>FineCMS公益软件 v<?php echo DR_VERSION; ?></span></p>
</footer>
<!--侧边导航-->
<ul class="layui-nav layui-nav-tree layui-nav-side blog-nav-left layui-hide" lay-filter="nav">
    <li class="layui-nav-item <?php if ($indexc) { ?>layui-this<?php } ?>">
        <a href="/">网站首页</a>
    </li>
    <?php $rt = $this->list_tag("action=category pid=0"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
    <li class="layui-nav-item <?php if (in_array($catid, $t['catids'])) { ?>layui-this<?php } ?>">
        <a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a>
    </li>
    <?php } } ?>
</ul>
<!--分享窗体-->
<div class="blog-share layui-hide">
    <div class="blog-share-body">
        <div style="width: 200px;height:100%;">
            <div class="bdsharebuttonbox">
                <a class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
                <a class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
                <a class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
                <a class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a>
            </div>
        </div>
    </div>
</div>
<!--遮罩-->
<div class="blog-mask animated layui-hide"></div>

</body>
</html>