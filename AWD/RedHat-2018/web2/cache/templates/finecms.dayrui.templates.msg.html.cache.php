<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title><?php echo fc_lang('提示信息'); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta content="www.dayrui.com" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN THEME GLOBAL STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
	<!-- END THEME GLOBAL STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/pages/css/error.min.css" rel="stylesheet" type="text/css" />
	<!-- END PAGE LEVEL STYLES -->
	<!-- BEGIN THEME LAYOUT STYLES -->
	<!-- END THEME LAYOUT STYLES -->
	<style>
		<?php if (IS_MOBILE) { ?>
		.details {
			clear: both !important;
			display: block !important;
			text-align: center !important;
			padding-top: 10px !important;
			margin-left: 0 !important;
		}
		.page-404 .number {
			letter-spacing:0px;

		}
		<?php } ?>
	</style>
</head>
<!-- END HEAD -->
<body class=" page-404-full-page">
<div class="row">
	<div class="col-md-12 page-404">
		<?php if ($mark==1) { ?>
		<div class="number font-green-turquoise" style="top: 20px;"> <i class="fa fa-check-circle-o"></i> </div>
		<?php } else if ($mark==2) { ?>
		<div class="number font-blue" style="top: 20px;"> <i class="fa  fa-info-circle"></i> </div>
		<?php } else { ?>
		<div class="number font-red" style="top: 20px;"> <i class="fa fa-times-circle-o"></i> </div>
		<?php } ?>
		<div class="details">
			<h4><?php echo $msg; ?></h4>
			<p class="alert_btnleft">
				<?php if ($url) { ?>
				<a href="<?php echo $url; ?>"><?php echo fc_lang('如果您的浏览器没有自动跳转，请点击这里'); ?></a>
				<meta http-equiv="refresh" content="<?php echo $time; ?>; url=<?php echo $url; ?>">
				<?php } else { ?>
				<a href="javascript:history.back();" >[<?php echo fc_lang('点击返回上一页'); ?>]</a>
				<?php } ?>
			</p>

		</div>
	</div>
</div>
<!--[if lt IE 9]>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/respond.min.js"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/excanvas.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/scripts/app.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		top.$('.page-loading').remove();
		<?php if (is_array($cache_url)) { $count=count($cache_url);foreach ($cache_url as $t) { ?>
		$.ajax({
			type: "GET",
			url: "<?php echo $t['url']; ?>&"+Math.random(),
			dataType: "text",
			success: function (data) {
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {
			}
		});
		<?php } } ?>
	});
</script>
</body>
</html>