<?php $indexc=$catid ? 0 : 1; if ($fn_include = $this->_include("header.html", "/")) include($fn_include); ?>


<script type="text/javascript">
    $(function() {
        <?php if ($result) { ?>
        dr_tips('<?php echo $result['msg']; ?>');
        <?php } ?>
    });
</script>

<div class="blog-body">
	<div class="blog-container">
		<blockquote class="layui-elem-quote sitemap layui-breadcrumb shadow">
			<a href="/" title="网站首页">网站首页</a>
			<a><cite><?php echo $form['name']; ?></cite></a>
		</blockquote>
		<div class="blog-main">
			<div class="row">
				<div class="col-md-12">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subject font-dark bold uppercase"><?php echo $form['name']; ?></span>
							</div>
						</div>
						<div class="portlet-body">
							<form action="" class="form-horizontal" method="post" name="myform" id="myform">
								<?php echo $myfield;  if ($code) { ?>
								<div class="form-group">
									<label class="control-label col-md-2">验证码</label>
									<div class="col-md-3">
										<label>
											<div class="form-recaptcha">
												<div class="input-group">
													<input type="text" class="form-control" name="code">
													<div class="input-group-btn fc-code">
														<?php echo dr_code(120, 35); ?>
													</div>
												</div>
											</div>
										</label>
									</div>
								</div>
								<?php } ?>

								<div class="portlet-body form myfooter">
									<div class="form-actions text-center">
										<button type="submit" class="btn green"> <i class="fa fa-save"></i> 提交内容</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>