<?php $indexc=1; if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
<div class="blog-body">
    <div class="blog-container">
        <blockquote class="layui-elem-quote sitemap layui-breadcrumb shadow">
            <a href="/" title="网站首页">网站首页</a>
            <a><cite>404</cite></a>
        </blockquote>
        <div class="blog-main">
            <link href="<?php echo THEME_PATH; ?>admin/pages/css/error.min.css" rel="stylesheet" type="text/css" />
            <div class="page-container">
                <div class="page-content">
                    <div class="container">
                        <!-- BEGIN PAGE BREADCRUMBS -->
                        <!-- END PAGE BREADCRUMBS -->
                        <!-- BEGIN PAGE CONTENT INNER -->
                        <div class="page-content-inner">
                            <div class="row">
                                <div class="col-md-12 page-404">
                                    <div class="number font-green"> 404 </div>
                                    <div class="details">
                                        <h3>没有找到您要访问的页面</h3>
                                        <p> <?php echo $msg; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END PAGE CONTENT INNER -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>