<?php /* Smarty version 3.1.26, created on 2018-10-19 21:46:59
         compiled from "templates\error.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:109085bc9e053b11fd3_21581065%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cf9f9b9355e452982852be7006ceb40f64668a6a' => 
    array (
      0 => 'templates\\error.tpl',
      1 => 1509088763,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '109085bc9e053b11fd3_21581065',
  'has_nocache_code' => false,
  'version' => '3.1.26',
  'unifunc' => 'content_5bc9e053b4f1e8_14263468',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5bc9e053b4f1e8_14263468')) {
function content_5bc9e053b4f1e8_14263468 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '109085bc9e053b11fd3_21581065';
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
	<div style="margin:0 auto;width:50%" class="alert alert-warning">
  <button type="button" class="close" data-dismiss="alert"></button>
  <strong>Warning!</strong> Best check yo self, you're not looking too good.
</div>
	</body>
</html><?php }
}
?>