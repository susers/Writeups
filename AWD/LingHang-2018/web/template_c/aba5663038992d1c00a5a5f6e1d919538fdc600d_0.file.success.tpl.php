<?php /* Smarty version 3.1.26, created on 2018-10-20 16:33:43
         compiled from "templates\success.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:52785bcae8675980f0_15414385%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aba5663038992d1c00a5a5f6e1d919538fdc600d' => 
    array (
      0 => 'templates\\success.tpl',
      1 => 1540024326,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '52785bcae8675980f0_15414385',
  'has_nocache_code' => false,
  'version' => '3.1.26',
  'unifunc' => 'content_5bcae8675e7c98_58956978',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5bcae8675e7c98_58956978')) {
function content_5bcae8675e7c98_58956978 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '52785bcae8675980f0_15414385';
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