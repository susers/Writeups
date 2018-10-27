<?php /* Smarty version 3.1.26, created on 2018-10-21 20:15:19
         compiled from "templates/success.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:13667148755bcd40c72bfb19_38210264%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '103126891ba927f98e721043c3e972a7bddc12a9' => 
    array (
      0 => 'templates/success.tpl',
      1 => 1540078328,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13667148755bcd40c72bfb19_38210264',
  'has_nocache_code' => false,
  'version' => '3.1.26',
  'unifunc' => 'content_5bcd40c72e0210_77913237',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5bcd40c72e0210_77913237')) {
function content_5bcd40c72e0210_77913237 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '13667148755bcd40c72bfb19_38210264';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="favicon.ico">
		<title>星球管理系统</title>
		<!-- Bootstrap core CSS -->

		<link href="./public/css/bootstrap.min.css" rel="stylesheet">
		<?php echo '<script'; ?>
 src="./public/js/jquery.min.js"><?php echo '</script'; ?>
>
        <style>
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
  text-align:center;
}

</style>
	</head>
	<body>
	<div style="margin:0 auto;width:50%" class="alert alert-success">
	  <button type="button" class="close" data-dismiss="alert"></button>
	  <strong>successful!</strong>your oprating is ok！
	</div>
	</body>
</html><?php }
}
?>