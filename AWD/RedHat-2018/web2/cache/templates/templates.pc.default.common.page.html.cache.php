<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>

<link href="<?php echo HOME_THEME_PATH; ?>css/about.css" rel="stylesheet" />

<div class="blog-body">
	<div class="blog-container">
		<blockquote class="layui-elem-quote sitemap layui-breadcrumb shadow">
			<a href="/" title="网站首页">网站首页</a>
			<?php echo dr_catpos($catid, '', true, '<a href="[url]">[name]</a>'); ?>
		</blockquote>
		<div class="blog-main">
			<div class="layui-tab layui-tab-brief shadow">
				<ul class="layui-tab-title">
					<!--循环同级栏目或者子栏目-->
					<?php if (is_array($related)) { $count=count($related);foreach ($related as $c) { ?>
					<li <?php if (in_array($catid, $c['catids'])) { ?>class="layui-this"<?php } ?>><a href="<?php echo $c['url']; ?>"><b><?php echo $c['name']; ?></b></a></li>
					<?php } } ?>
				</ul>
				<div class="layui-tab-content" style="padding: 20px;">
					<?php echo $content; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>