<?php /* Smarty version 3.1.26, created on 2018-10-20 19:23:58
         compiled from "templates\index.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:165455bcb104e285908_92989840%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f90be83b235fb03cc225b11607032e9ddd415899' => 
    array (
      0 => 'templates\\index.tpl',
      1 => 1540034585,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '165455bcb104e285908_92989840',
  'has_nocache_code' => false,
  'version' => '3.1.26',
  'unifunc' => 'content_5bcb104e2e0589_95316976',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5bcb104e2e0589_95316976')) {
function content_5bcb104e2e0589_95316976 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '165455bcb104e285908_92989840';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>学生会-简历系统</title>
  <!-- <meta name="description" content="particles.js is a lightweight JavaScript library for creating particles.">
  <meta name="author" content="Vincent Garreau" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"> -->
  <link rel="stylesheet" media="screen" href="public/css/style.css">
	<link rel="stylesheet" type="text/css" href="public/css/reset.css"/>
	<style>
	#particles-js{
		width: 100%;
		height: 100%;
		position: relative;
		background-image: url(public/image/bg.jpg);
		background-position: 50% 50%;
		background-size: cover;
		background-repeat: no-repeat;
		margin-left: auto;
		margin-right: auto;
	}
	</style>
</head>
<body>
<form   class="form-signin" id="test" method="post"  action="./index.php?c=User&a=login">
<div id="particles-js">
		<div class="login">
			<div class="login-top" style="margin-top: 100px">
				学生会-简历系统
			</div>
			<div class="login-center clearfix">
				<div class="login-center-img"><img src="public/image/name.png"/></div>
				<div class="login-center-input">
					<input type="text" name="username" placeholder="请输入您的用户名" onfocus="this.placeholder=''" onblur="this.placeholder='请输入您的用户名'" required autofocus/>
					<div class="login-center-input-text">用户名</div>
				</div>
			</div>
			<div class="login-center clearfix">
				<div class="login-center-img"><img src="public/image/password.png"/></div>
				<div class="login-center-input">
					<input type="password" name="password" placeholder="请输入您的密码" onfocus="this.placeholder=''" onblur="this.placeholder='请输入您的密码'" required/>
					<div class="login-center-input-text">密码</div>
				</div>
			</div>
			<div class="login-button">
			          <input  style= "width:100%;height:100%;color:white;background-color:transparent;border:0;" type="submit" value="登陆">
			</div>			
				<a href="./index.php?c=User&a=register"><div class="login-button">注册</div></a>
			
		</div>
		<div class="sk-rotating-plane"></div>
</div>

<!-- scripts -->
<?php echo '<script'; ?>
 src="public/js/particles.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="public/js/app.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript">
	function hasClass(elem, cls) {
	  cls = cls || '';
	  if (cls.replace(/\s/g, '').length == 0) return false; //当cls没有参数时，返回false
	  return new RegExp(' ' + cls + ' ').test(' ' + elem.className + ' ');
	}
	 
	function addClass(ele, cls) {
	  if (!hasClass(ele, cls)) {
	    ele.className = ele.className == '' ? cls : ele.className + ' ' + cls;
	  }
	}
	 
	function removeClass(ele, cls) {
	  if (hasClass(ele, cls)) {
	    var newClass = ' ' + ele.className.replace(/[\t\r\n]/g, '') + ' ';
	    while (newClass.indexOf(' ' + cls + ' ') >= 0) {
	      newClass = newClass.replace(' ' + cls + ' ', ' ');
	    }
	    ele.className = newClass.replace(/^\s+|\s+$/g, '');
	  }
	}
<?php echo '</script'; ?>
>
</form>
</body>
</html><?php }
}
?>